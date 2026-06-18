<?php

namespace Lucinda\Console\Language;

final class ElementNode implements Node
{
    private string $name;
    /**
     * @var array<string,string|bool>
     */
    private array $attributes;
    /**
     * @var Node[]
     */
    private array $children;
    private SourcePosition $position;

    /**
     * @param array<string,string|bool> $attributes
     * @param Node[]                    $children
     */
    public function __construct(
        string $name,
        array $attributes,
        array $children,
        SourcePosition $position
    ) {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->children = $children;
        $this->position = $position;
    }

    public function getPosition(): SourcePosition
    {
        return $this->position;
    }

    public function getAttribute(string $name, string|bool|null $default = null): string|bool|null
    {
        return $this->attributes[$name] ?? $default;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string,string|bool>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
