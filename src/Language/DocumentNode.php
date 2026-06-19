<?php

namespace Lucinda\Console\Language;

/**
 * Root node containing the parsed console document tree.
 */
final class DocumentNode implements Node
{
    /**
     * @var Node[]
     */
    private array $children = [];
    private SourcePosition $position;

    /**
     * Creates a document with optional children and source position metadata.
     *
     * @param Node[] $children
     * @param ?SourcePosition $position
     * @return void
     */
    public function __construct(
        array $children = [],
        ?SourcePosition $position = null
    ) {
        $this->children = $children;
        $this->position = $position ?? new SourcePosition(1, 1);
    }

    /**
     * Returns the document source position.
     *
     * @return SourcePosition
     */
    public function getPosition(): SourcePosition
    {
        return $this->position;
    }

    /**
     * Appends a child node to the document.
     *
     * @param Node $child
     * @return void
     */
    public function addChild(Node $child): void
    {
        $this->children[] = $child;
    }

    /**
     * Replaces all document children.
     *
     * @param Node[] $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * Returns the document children in render order.
     *
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
