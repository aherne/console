<?php

namespace Lucinda\Console\Language;

final class Tokenizer
{
    private int $offset = 0;
    private int $line = 1;
    private int $column = 1;
    private int $length;

    public function __construct(private readonly string $source)
    {
        $this->length = strlen($source);
    }

    /**
     * @return Token[]
     */
    public function tokenize(): array
    {
        $tokens = [];
        while (!$this->isAtEnd()) {
            if ($this->startsWith("<!--")) {
                $this->consumeComment();
            } elseif ($this->peek() === "<") {
                $tokens[] = $this->consumeTag();
            } else {
                $tokens[] = $this->consumeText();
            }
        }
        return $tokens;
    }

    private function consumeComment(): void
    {
        $position = $this->position();
        $end = strpos($this->source, "-->", $this->offset+4);
        if ($end === false) {
            throw new ParseException("Unclosed comment", $position);
        }
        $this->advance(substr($this->source, $this->offset, $end+3-$this->offset));
    }

    private function consumeText(): Token
    {
        $position = $this->position();
        $end = strpos($this->source, "<", $this->offset);
        if ($end === false) {
            $end = $this->length;
        }
        $value = substr($this->source, $this->offset, $end-$this->offset);
        $this->advance($value);

        return new Token(
            TokenType::TEXT,
            html_entity_decode($value, ENT_QUOTES | ENT_HTML5, "UTF-8"),
            [],
            $position
        );
    }

    private function consumeTag(): Token
    {
        $position = $this->position();
        $this->advance("<");

        if ($this->peek() === "!") {
            throw new ParseException("Declarations are not allowed", $position);
        }

        if ($this->peek() === "/") {
            $this->advance("/");
            $this->skipWhitespace();
            $name = $this->consumeName($position);
            $this->skipWhitespace();
            $this->expect(">", $position);
            return new Token(TokenType::CLOSE_TAG, strtolower($name), [], $position);
        }

        $this->skipWhitespace();
        $name = strtolower($this->consumeName($position));
        $attributes = [];

        $selfClosing = $this->traverse($attributes);
        

        if ($this->isAtEnd() && !str_ends_with($this->source, ">")) {
            throw new ParseException("Unclosed tag: ".$name, $position);
        }

        return new Token(
            $selfClosing ? TokenType::SELF_CLOSING_TAG : TokenType::OPEN_TAG,
            $name,
            $attributes,
            $position
        );
    }

    private function consumeName(SourcePosition $position): string
    {
        $start = $this->offset;
        while (!$this->isAtEnd() && preg_match('/[a-zA-Z0-9:_-]/', $this->peek()) === 1) {
            $this->advance($this->peek());
        }
        if ($start === $this->offset) {
            throw new ParseException("Expected a name", $position);
        }
        return substr($this->source, $start, $this->offset-$start);
    }

    private function expect(string $value, SourcePosition $position): void
    {
        if (!$this->startsWith($value)) {
            throw new ParseException("Expected ".$value, $position);
        }
        $this->advance($value);
    }

    private function skipWhitespace(): void
    {
        while (!$this->isAtEnd() && preg_match('/\s/', $this->peek()) === 1) {
            $this->advance($this->peek());
        }
    }

    private function startsWith(string $value): bool
    {
        return substr($this->source, $this->offset, strlen($value)) === $value;
    }

    private function peek(): string
    {
        return $this->source[$this->offset] ?? "";
    }

    private function advance(string $value): void
    {
        $length = strlen($value);
        for ($i = 0; $i < $length; $i++) {
            if ($value[$i] === "\n") {
                $this->line++;
                $this->column = 1;
            } else {
                $this->column++;
            }
        }
        $this->offset += $length;
    }

    private function isAtEnd(): bool
    {
        return $this->offset >= $this->length;
    }

    private function position(): SourcePosition
    {
        return new SourcePosition($this->line, $this->column);
    }

    private function traverse(array &$attributes): bool
    {
        while (!$this->isAtEnd()) {
            $this->skipWhitespace();
            if ($this->startsWith("/>")) {
                $this->advance("/>");
                return true;
            }
            if ($this->peek() === ">") {
                $this->advance(">");
                break;
            }

            $attributePosition = $this->position();
            $attributeName = strtolower($this->consumeName($attributePosition));
            if (array_key_exists($attributeName, $attributes)) {
                throw new ParseException("Duplicate attribute: ".$attributeName, $attributePosition);
            }

            $this->skipWhitespace();
            if ($this->peek() !== "=") {
                $attributes[$attributeName] = true;
                continue;
            }

            $this->advance("=");
            $this->skipWhitespace();
            $quote = $this->peek();
            if ($quote !== '"' && $quote !== "'") {
                throw new ParseException("Attribute values must be quoted", $this->position());
            }
            $this->advance($quote);
            $start = $this->offset;
            while (!$this->isAtEnd() && $this->peek() !== $quote) {
                $this->advance($this->peek());
            }
            if ($this->isAtEnd()) {
                throw new ParseException("Unclosed attribute value", $attributePosition);
            }
            $value = substr($this->source, $start, $this->offset-$start);
            $this->advance($quote);
            $attributes[$attributeName] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, "UTF-8");
        }
        return false;
    }
}
