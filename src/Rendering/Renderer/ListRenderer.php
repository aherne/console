<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\Node;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Styling\Style;

/**
 * Renders ordered and unordered lists, including nested lists.
 */
final class ListRenderer extends ContextAware
{
    private const CORRESPONDENCES = [
        1000 => "m", 900 => "cm", 500 => "d", 400 => "cd", 100 => "c", 90 => "xc",
        50 => "l", 40 => "xl", 10 => "x", 9 => "ix", 5 => "v", 4 => "iv", 1 => "i"
        ];

    /**
     * Renders a list element with markers and nested list indentation.
     *
     * @return string[]
     * @param ElementNode $list
     * @param int $width
     * @param int $depth
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function render(ElementNode $list, int $width, int $depth = 0): array
    {
        $lines = [];
        $items = array_values(array_filter($list->getChildren(), fn (Node $node) => $node instanceof ElementNode && $node->getName() === "li"));
        $start = max(1, (int) $list->getAttribute("start", "1"));
        foreach ($items as $index => $item) {
            $marker = $this->listMarker($list, $start+$index);
            $nested = [];
            $inline = [];
            foreach ($item->getChildren() as $child) {
                if ($child instanceof ElementNode && in_array($child->getName(), ["ol", "ul"], true)) {
                    $nested[] = $child;
                } else {
                    $inline[] = $child;
                }
            }
            $prefix = str_repeat(" ", $depth*2).$marker." ";
            $contentWidth = max(1, $width-$this->context->getDisplayWidth()->get($prefix));
            $inlineRenderer = new InlineRenderer($this->context);
            $itemLines = $inlineRenderer->render($inline, $contentWidth, new Style());
            foreach ($itemLines as $lineIndex => $line) {
                $lines[] = ($lineIndex === 0 ? $prefix : str_repeat(" ", $this->context->getDisplayWidth()->get($prefix))).$line;
            }
            foreach ($nested as $childList) {
                array_push($lines, ...$this->render($childList, $width, $depth+1));
            }
        }
        return $lines;
    }

    /**
     * Resolves the marker string for a list item.
     *
     * @param ElementNode $list
     * @param int $number
     * @return string
     */
    private function listMarker(ElementNode $list, int $number): string
    {
        $marker = (string) $list->getAttribute("marker", $list->getName() === "ol" ? "decimal" : "bullet");
        return match ($marker) {
            "dash" => "-",
            "checkmark" => $this->context->getEnvironment()->getUnicode() ? "\u{2713}" : "[x]",
            "alphabetic" => chr(96+(($number-1)%26)+1).".",
            "roman" => $this->roman($number).".",
            "bullet" => $this->context->getEnvironment()->getUnicode() ? "\u{2022}" : "*",
            "decimal" => $number.".",
            default => $marker
        };
    }

    /**
     * Converts a positive integer to a lowercase roman numeral.
     *
     * @param int $number
     * @return string
     */
    private function roman(int $number): string
    {
        $output = "";
        foreach (self::CORRESPONDENCES as $value => $symbol) {
            while ($number >= $value) {
                $output .= $symbol;
                $number -= $value;
            }
        }
        return $output;
    }
}
