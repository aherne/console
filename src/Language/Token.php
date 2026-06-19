<?php

namespace Lucinda\Console\Language;

/**
 * Represents one lexical token emitted from console markup.
 */
final class Token
{
    private TokenType $type;
    private string $value;
    /**
     * @var array<string,string|bool>
     */
    private array $attributes;
    private SourcePosition $position;

    /**
     * Creates a token with its type, value, parsed attributes, and source position.
     *
     * @param array<string,string|bool> $attributes
     * @param TokenType $type
     * @param string $value
     * @param SourcePosition $position
     * @return void
     */
    public function __construct(
        TokenType $type,
        string $value,
        array $attributes,
        SourcePosition $position
    ) {
        $this->type = $type;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->position = $position;
    }

    /**
     * Returns the token type.
     *
     * @return TokenType
     */
    public function getType(): TokenType
    {
        return $this->type;
    }

    /**
     * Returns the tag name or decoded text value for the token.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns parsed attributes for tag tokens.
     *
     * @return array<string,string|bool>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns where this token starts in the source.
     *
     * @return SourcePosition
     */
    public function getPosition(): SourcePosition
    {
        return $this->position;
    }
}
