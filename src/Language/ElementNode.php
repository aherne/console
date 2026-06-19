<?php

namespace Lucinda\Console\Language;

/**
 * Represents a parsed markup element with attributes, children, and source position.
 */
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
     * Creates an element node from tokenizer output.
     *
     * @param array<string,string|bool> $attributes
     * @param Node[]                    $children
     * @param string $name
     * @param SourcePosition $position
     * @return void
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

    /**
     * Returns the element source position.
     *
     * @return SourcePosition
     */
    public function getPosition(): SourcePosition
    {
        return $this->position;
    }

    /**
     * Returns an attribute value or the provided default when it is missing.
     *
     * @param string $name
     * @param string|bool|null $default
     * @return string|bool|null
     */
    public function getAttribute(string $name, string|bool|null $default = null): string|bool|null
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Returns the normalized tag name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns all element attributes.
     *
     * @return array<string,string|bool>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Appends a child node.
     *
     * @param Node $child
     * @return void
     */
    public function addChild(Node $child): void
    {
        $this->children[] = $child;
    }

    /**
     * Replaces all child nodes.
     *
     * @param Node[] $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * Returns all child nodes in source order.
     *
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
