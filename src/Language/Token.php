<?php

namespace Lucinda\Console\Language;

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
     * @param array<string,string|bool> $attributes
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

    public function getType(): TokenType
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array<string,string|bool>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getPosition(): SourcePosition
    {
        return $this->position;
    }
}
