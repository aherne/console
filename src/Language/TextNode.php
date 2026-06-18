<?php

namespace Lucinda\Console\Language;

final class TextNode implements Node
{
    private string $value;
    private SourcePosition $position;

    public function __construct(
        string $value,
        SourcePosition $position
    ) {
        $this->value = $value;
        $this->position = $position;
    }

    public function getPosition(): SourcePosition
    {
        return $this->position;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
