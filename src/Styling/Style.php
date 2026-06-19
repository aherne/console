<?php

namespace Lucinda\Console\Styling;

/**
 * Immutable collection of resolved style properties.
 */
final class Style
{
    /**
     * Creates a style from raw property values.
     *
     * @param array<string,int|string|bool|null> $properties
     * @return void
     */
    public function __construct(private readonly array $properties = [])
    {
    }

    /**
     * Returns a new style with the other style overriding current properties.
     *
     * @param self $other
     * @return self
     */
    public function merge(self $other): self
    {
        return new self(array_replace($this->properties, $other->properties));
    }

    /**
     * Returns a property value or the provided default.
     *
     * @param string $name
     * @param int|string|bool|null $default
     * @return int|string|bool|null
     */
    public function get(string $name, int|string|bool|null $default = null): int|string|bool|null
    {
        return $this->properties[$name] ?? $default;
    }

    /**
     * Returns all raw style properties.
     *
     * @return array<string,int|string|bool|null>
     */
    public function all(): array
    {
        return $this->properties;
    }
}
