<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Language\ElementNode;

final class WidthResolver extends ContextAware
{    
    /** @param ElementNode[] $elements @return int[] */
    public function allocateWidths(array $elements, int $available): array
    {
        if ($elements === []) {
            return [];
        }
        
        $widths = array_fill(0, count($elements), 0);
        $flex = [];
        $remaining = $available;
        foreach ($elements as $index => $element) {
            $value = $element->getAttribute("width");
            if (is_string($value) && str_ends_with($value, "%")) {
                $widths[$index] = max(1, (int) floor($available*(float) rtrim($value, "%")/100));
                $remaining -= $widths[$index];
            } elseif (is_string($value) && ctype_digit($value)) {
                $widths[$index] = max(1, (int) $value);
                $remaining -= $widths[$index];
            } else {
                $flex[] = $index;
            }
        }
        $share = max(1, intdiv(max(0, $remaining), max(1, count($flex))));
        foreach ($flex as $index) {
            $widths[$index] = $share;
        }
        while (array_sum($widths) < $available) {
            $widths[array_sum($widths)%count($widths)]++;
        }
        while (array_sum($widths) > $available) {
            $index = array_keys($widths, max($widths), true)[0];
            if ($widths[$index] <= 1) {
                break;
            }
            $widths[$index]--;
        }
        return $widths;
    }

    public function resolveWidth(ElementNode $element, int $available): int
    {
        $style = $this->context->getStyles()->resolve($element);
        $value = $style->get("width", $element->getAttribute("width"));
        $width = $available;
        if (is_string($value) && str_ends_with($value, "%")) {
            $width = (int) floor($available*(float) rtrim($value, "%")/100);
        } elseif (is_string($value) && ctype_digit($value)) {
            $width = (int) $value;
        }
        $min = (int) $style->get("min-width", $element->getAttribute("min-width", "1"));
        $max = (int) $style->get("max-width", $element->getAttribute("max-width", (string) $available));
        return max(1, min($available, $max, max($min, $width)));
    }
}