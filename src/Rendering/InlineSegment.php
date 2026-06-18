<?php

namespace Lucinda\Console\Rendering;

use Lucinda\Console\Styling\Style;

final class InlineSegment
{
    private string $text;
    private Style $style;
    private ?string $href;

    public function __construct(
        string $text,
        Style $style,
        ?string $href = null
    ) {
        $this->text = $text;
        $this->style = $style;
        $this->href = $href;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }
}
