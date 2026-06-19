<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Rendering\Renderer\Utilities\BorderCharacters;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\TextLayout;

/**
 * Renders bordered or unbordered box elements around nested content.
 */
final class BoxRenderer extends ContextAware
{
    /**
     * Renders a box element and its children within a fixed width.
     *
     * @return string[]
     * @param ElementNode $element
     * @param int $width
     * @param NodesRenderer $nodesRenderer
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function render(ElementNode $element, int $width, NodesRenderer $nodesRenderer): array
    {
        $borderCharacters = new BorderCharacters($this->context);
        $textLayout = new TextLayout($this->context);

        $style = $this->context->getStyles()->resolve($element);
        $padding = max(0, (int) $style->get("padding", $element->getAttribute("padding", "0")));
        $border = (string) $element->getAttribute("border", "single");
        $characters = $borderCharacters->borderCharacters($border);
        $hasBorder = $border !== "none";
        $innerWidth = max(1, $width-2*$padding-($hasBorder ? 2 : 0));
        $content = $nodesRenderer->render($element->getChildren(), $innerWidth);
        if ($content === []) {
            $content = [""];
        }
        $lines = [];
        if ($hasBorder) {
            $title = trim((string) $element->getAttribute("title", ""));
            $top = str_repeat($characters[4], max(0, $width-2));
            if ($title !== "") {
                $label = " ".$title." ";
                $top = mb_substr($label.$top, 0, max(0, $width-2), "UTF-8");
                $top .= str_repeat($characters[4], max(0, $width-2-$this->context->getDisplayWidth()->get($top)));
            }
            $lines[] = $characters[0].$top.$characters[1];
        }
        for ($i = 0; $i < $padding; $i++) {
            $lines[] = ($hasBorder ? $characters[5] : "").str_repeat(" ", $width-($hasBorder ? 2 : 0)).($hasBorder ? $characters[5] : "");
        }
        foreach ($content as $line) {
            $body = str_repeat(" ", $padding).$textLayout->pad($line, $innerWidth).str_repeat(" ", $padding);
            $lines[] = ($hasBorder ? $characters[5] : "").$body.($hasBorder ? $characters[5] : "");
        }
        for ($i = 0; $i < $padding; $i++) {
            $lines[] = ($hasBorder ? $characters[5] : "").str_repeat(" ", $width-($hasBorder ? 2 : 0)).($hasBorder ? $characters[5] : "");
        }
        if ($hasBorder) {
            $lines[] = $characters[2].str_repeat($characters[4], max(0, $width-2)).$characters[3];
        }
        return $lines;
    }
}
