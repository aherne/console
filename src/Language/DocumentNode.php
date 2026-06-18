<?php

namespace Lucinda\Console\Language;

final class DocumentNode implements Node
{
    /**
     * @var Node[]
     */
    private array $children = [];
    private SourcePosition $position;

    /**
     * @param Node[] $children
     */
    public function __construct(
        array $children = [],
        ?SourcePosition $position = null
    ) {
        $this->children = $children;
        $this->position = $position ?? new SourcePosition(1, 1);
    }

    public function getPosition(): SourcePosition
    {
        return $this->position;
    }

    public function addChild(Node $child): void
    {
        $this->children[] = $child;
    }

    /**
     * @param Node[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
