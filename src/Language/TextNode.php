<?php

namespace Lucinda\Console\Language;

/**
 * Represents decoded text content in the parsed document tree.
 */
final class TextNode implements Node
{
    private string $value;
    private SourcePosition $position;

    /**
     * Creates a text node with its source position.
     *
     * @param string $value
     * @param SourcePosition $position
     * @return void
     */
    public function __construct(
        string $value,
        SourcePosition $position
    ) {
        $this->value = $value;
        $this->position = $position;
    }

    /**
     * Returns the text source position.
     *
     * @return SourcePosition
     */
    public function getPosition(): SourcePosition
    {
        return $this->position;
    }

    /**
     * Returns the decoded text value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
