<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\WidthResolver;

/**
 * Renders a static progress bar line.
 */
final class ProgressRenderer extends ContextAware
{
    /**
     * Renders a progress element within the available width.
     *
     * @param ElementNode $element
     * @param int $width
     * @return string
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function render(ElementNode $element, int $width): string
    {
        $utility = new WidthResolver($this->context);

        $value = (float) $element->getAttribute("value", "0");
        $max = max(0.00001, (float) $element->getAttribute("max", "100"));
        $ratio = max(0, min(1, $value/$max));
        $label = (string) $element->getAttribute("label", (string) round($ratio*100)."%");
        $barWidth = max(3, min(
            $width-$this->context->getDisplayWidth()->get($label)-3,
            $utility->resolveWidth($element, $width)-$this->context->getDisplayWidth()->get($label)-3
            ));
        $filled = (int) round($barWidth*$ratio);
        $full = $this->context->getEnvironment()->getUnicode() ? "\u{2588}" : "#";
        $empty = $this->context->getEnvironment()->getUnicode() ? "\u{2591}" : "-";
        return "[".str_repeat($full, $filled).str_repeat($empty, $barWidth-$filled)."] ".$label;
    }
}
