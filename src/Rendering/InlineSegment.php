<?php

namespace Lucinda\Console\Rendering;

use Lucinda\Console\Styling\Style;

/**
 * Carries a run of inline text with a single style and optional hyperlink.
 */
final class InlineSegment
{
    private string $text;
    private Style $style;
    private ?string $href;

    /**
     * Creates a styled inline text segment.
     *
     * @param string $text
     * @param Style $style
     * @param ?string $href
     * @return void
     */
    public function __construct(
        string $text,
        Style $style,
        ?string $href = null
    ) {
        $this->text = $text;
        $this->style = $style;
        $this->href = $href;
    }

    /**
     * Returns the segment text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Returns the segment style.
     *
     * @return Style
     */
    public function getStyle(): Style
    {
        return $this->style;
    }

    /**
     * Returns the optional hyperlink target.
     *
     * @return ?string
     */
    public function getHref(): ?string
    {
        return $this->href;
    }
}
