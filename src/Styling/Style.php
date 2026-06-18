<?php

namespace Lucinda\Console\Styling;

final class Style
{
    /**
     * @param array<string,int|string|bool|null> $properties
     */
    public function __construct(private readonly array $properties = [])
    {
    }

    public function merge(self $other): self
    {
        return new self(array_replace($this->properties, $other->properties));
    }

    public function get(string $name, int|string|bool|null $default = null): int|string|bool|null
    {
        return $this->properties[$name] ?? $default;
    }

    /**
     * @return array<string,int|string|bool|null>
     */
    public function all(): array
    {
        return $this->properties;
    }
}
