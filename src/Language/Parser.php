<?php

namespace Lucinda\Console\Language;

/**
 * Converts console markup text into a validated document tree.
 */
final class Parser
{
    private const VOID_ELEMENTS = ["br", "hr", "spacer", "progress"];

    /**
     * Creates a parser with the validator used after token parsing.
     *
     * @param Validator $validator
     * @return void
     */
    public function __construct(private readonly Validator $validator = new Validator())
    {
    }

    /**
     * Parses markup into a document, enforcing size and node-count limits.
     *
     * @param string $source
     * @return DocumentNode
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function parse(string $source): DocumentNode
    {
        if (strlen($source) > 1024*1024) {
            throw new ParseException("Maximum input size exceeded", new SourcePosition(1, 1));
        }
        $tokens = (new Tokenizer($source))->tokenize();
        if (count($tokens) > 10000) {
            throw new ParseException("Maximum node count exceeded", new SourcePosition(1, 1));
        }
        $document = new DocumentNode();
        $stack = [$document];

        foreach ($tokens as $token) {
            $this->parseToken($token, $stack);
        }

        if (count($stack) !== 1) {
            $element = $stack[array_key_last($stack)];
            if (!$element instanceof ElementNode) {
                throw new ParseException("Unclosed document", $element->getPosition());
            }
            throw new ParseException("Unclosed tag: ".$element->getName(), $element->getPosition());
        }

        $this->validator->validate($document);
        return $document;
    }

    /**
     * Applies a token to the current open-element stack.
     *
     * @param array<int,DocumentNode|ElementNode> $stack
     * @param Token $token
     * @return void
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function parseToken(Token $token, array &$stack): void
    {
        $parent = $stack[array_key_last($stack)];
        if ($token->getType() === TokenType::TEXT) {
            $parent->addChild(new TextNode($token->getValue(), $token->getPosition()));
            return;
        }

        if ($token->getType() === TokenType::CLOSE_TAG) {
            if (count($stack) === 1) {
                throw new ParseException("Unexpected closing tag: ".$token->getValue(), $token->getPosition());
            }
            $open = $stack[array_key_last($stack)];
            if (!$open instanceof ElementNode || $open->getName() !== $token->getValue()) {
                $expected = $open instanceof ElementNode ? $open->getName() : "document";
                throw new ParseException(
                    "Closing tag ".$token->getValue()." does not match ".$expected,
                    $token->getPosition()
                );
            }
            array_pop($stack);
            return;
        }

        $element = new ElementNode($token->getValue(), $token->getAttributes(), [], $token->getPosition());
        $parent->addChild($element);
        $isVoid = in_array($element->getName(), self::VOID_ELEMENTS, true);
        if ($token->getType() === TokenType::OPEN_TAG && !$isVoid) {
            $stack[] = $element;
        }
    }
}
