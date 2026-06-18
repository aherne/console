<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\Node;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\TextLayout;
use Lucinda\Console\Rendering\Renderer\Utilities\WidthResolver;

final class ColumnsRenderer extends ContextAware
{
    public function render(ElementNode $element, int $width, NodesRenderer $nodesRenderer): array
    {
        $widthResolver = new WidthResolver($this->context);
        $textLayout = new TextLayout($this->context);

        $columns = array_values(array_filter($element->getChildren(), fn (Node $node) => $node instanceof ElementNode && $node->getName() === "column"));
        if ($columns === []) {
            return [];
        }
        $gap = max(0, (int) $element->getAttribute("gap", "1"));
        $usable = max(count($columns), $width-$gap*(count($columns)-1));
        $widths = $widthResolver->allocateWidths($columns, $usable);
        $rendered = [];
        $height = 0;
        foreach ($columns as $index => $column) {
            $rendered[$index] = $nodesRenderer->render($column->getChildren(), $widths[$index]);
            $height = max($height, count($rendered[$index]));
        }
        foreach ($columns as $index => $column) {
            $missing = $height-count($rendered[$index]);
            $verticalAlign = (string) $this->context->getStyles()->resolve($column)->get("vertical-align", "top");
            $before = $verticalAlign === "bottom" ? $missing : ($verticalAlign === "middle" ? intdiv($missing, 2) : 0);
            $after = $missing-$before;
            $rendered[$index] = array_merge(array_fill(0, $before, ""), $rendered[$index], array_fill(0, $after, ""));
        }
        $lines = [];
        for ($row = 0; $row < $height; $row++) {
            $parts = [];
            foreach ($columns as $index => $column) {
                $parts[] = $textLayout->pad($rendered[$index][$row] ?? "", $widths[$index]);
            }
            $lines[] = rtrim(implode(str_repeat(" ", $gap), $parts));
        }
        return $lines;
    }
}