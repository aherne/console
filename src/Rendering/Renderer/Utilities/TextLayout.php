<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

/**
 * Aligns and pads already-rendered terminal text by display width.
 */
final class TextLayout extends ContextAware
{
    /**
     * Aligns text within a target display width.
     *
     * @param string $value
     * @param int $width
     * @param string $align
     * @return string
     */
    public function align(string $value, int $width, string $align): string
    {
        $padding = max(0, $width-$this->context->getDisplayWidth()->get($value));
        return match ($align) {
            "right" => str_repeat(" ", $padding).$value,
            "center" => str_repeat(" ", intdiv($padding, 2)).$value.str_repeat(" ", $padding-intdiv($padding, 2)),
            default => $value.str_repeat(" ", $padding)
        };
    }

    /**
     * Right-pads text to the target display width.
     *
     * @param string $value
     * @param int $width
     * @return string
     */
    public function pad(string $value, int $width): string
    {
        return $value.str_repeat(" ", max(0, $width-$this->context->getDisplayWidth()->get($value)));
    }

}
