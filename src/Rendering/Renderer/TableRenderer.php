<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\Node;
use Lucinda\Console\Rendering\Renderer\Utilities\BorderCharacters;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\TextLayout;
use Lucinda\Console\Rendering\Renderer\Utilities\WidthResolver;
use Lucinda\Console\Styling\Style;

/**
 * Renders table elements with column widths, borders, headers, and cell alignment.
 */
final class TableRenderer extends ContextAware
{
    /**
     * Renders a table element into terminal lines.
     *
     * @return string[]
     * @param ElementNode $table
     * @param int $width
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function render(ElementNode $table, int $width): array
    {
        $rows = $this->getRows($table);
        if ($rows === []) {
            $empty = (string) $table->getAttribute("empty", "No data");
            return [$empty];
        }

        $utility = new BorderCharacters($this->context);

        $columnCount = max(array_map(fn (array $row) => array_sum(array_map(fn (ElementNode $cell) => max(1, (int) $cell->getAttribute("colspan", "1")), $row["cells"])), $rows));
        $borderName = (string) $table->getAttribute("border", "single");
        $border = $utility->tableBorderCharacters($borderName);
        $hasBorder = $borderName !== "none";
        $overhead = $hasBorder ? $columnCount+1 : max(0, $columnCount-1);
        $contentWidth = max($columnCount, $width-$overhead-2*$columnCount);
        $specifications = array_values(array_filter($table->getChildren(), fn (Node $node) => $node instanceof ElementNode && $node->getName() === "column"));
        $widths = $this->getWidths($specifications, $contentWidth, $columnCount);

        $lines = [];
        $separator = $this->tableSeparator($widths, $border, $hasBorder, "top");
        if ($separator !== "") {
            $lines[] = $separator;
        }
        foreach ($rows as $rowIndex => $row) {
            $this->parseRow($table, $columnCount, count($rows), $widths, $hasBorder, $rowIndex, $row, $border, $lines);
        }
        $bottom = $this->tableSeparator($widths, $border, $hasBorder, "bottom");
        if ($bottom !== "") {
            $lines[] = $bottom;
        }
        return $lines;
    }

    /**
     * Collects table row metadata from thead and tbody groups.
     *
     * @return array<int,array{header:bool,cells:ElementNode[]}>
     * @param ElementNode $table
     */
    private function getRows(ElementNode $table): array
    {
        $rows = [];
        foreach ($table->getChildren() as $group) {
            if (!$group instanceof ElementNode || !in_array($group->getName(), ["thead", "tbody"], true)) {
                continue;
            }
            foreach ($group->getChildren() as $row) {
                if ($row instanceof ElementNode && $row->getName() === "tr") {
                    $cells = array_values(array_filter($row->getChildren(), fn (Node $node) => $node instanceof ElementNode && in_array($node->getName(), ["th", "td"], true)));
                    $rows[] = ["header" => $group->getName() === "thead", "cells" => $cells];
                }
            }
        }
        return $rows;
    }

    /**
     * Resolves each table column width from column specifications or equal distribution.
     *
     * @param ElementNode[] $specifications
     *
     * @return int[]
     * @param int $contentWidth
     * @param int $columnCount
     */
    private function getWidths(array $specifications, int $contentWidth, int $columnCount): array
    {
        $utility = new WidthResolver($this->context);
        $widths = [];
        if (count($specifications) === $columnCount) {
            $widths = $utility->allocateWidths($specifications, $contentWidth);
        } else {
            $base = intdiv($contentWidth, $columnCount);
            $remainder = $contentWidth%$columnCount;
            $widths = array_fill(0, $columnCount, $base);
            for ($i = 0; $i < $remainder; $i++) {
                $widths[$i]++;
            }
        }
        return $widths;
    }

    /**
     * Renders one table row and appends its output lines and separator.
     *
     * @param int[]                                      $widths
     * @param array{header:bool,cells:ElementNode[]}     $row
     * @param string[]                                   $border
     * @param string[]                                   $lines
     * @param ElementNode $table
     * @param int $columnCount
     * @param int $rowsCount
     * @param bool $hasBorder
     * @param int $rowIndex
     * @return void
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function parseRow(
        ElementNode $table,
        int $columnCount,
        int $rowsCount,
        array $widths,
        bool $hasBorder,
        int $rowIndex,
        array $row,
        array $border,
        array &$lines
        ): void
    {
        $cellLines = [];
        $column = 0;
        $rowHeight = 1;
        $utility = new TextLayout($this->context);
        $inlineRenderer = new InlineRenderer($this->context);
        foreach ($row["cells"] as $cell) {
            if (!$cell instanceof ElementNode) {
                continue;
            }
            $span = min($columnCount-$column, max(1, (int) $cell->getAttribute("colspan", "1")));
            $cellWidth = array_sum(array_slice($widths, $column, $span))+2*($span-1)+($hasBorder ? $span-1 : 0);
            $zebra = $table->getAttribute("zebra") === true || strtolower((string) $table->getAttribute("zebra", "false")) === "true";
            $rowStyle = $row["header"]
                ? new Style(["bold" => true])
                : ($zebra && $rowIndex%2 === 1 ? new Style(["background" => "#202020"]) : new Style());
            $style = $this->context->getStyles()->resolve($cell)->merge($rowStyle);
            $cellLines[] = [
                "width" => $cellWidth,
                "lines" => $inlineRenderer->render($cell->getChildren(), $cellWidth, $style),
                "align" => (string) $style->get("align", $row["header"] ? "center" : "left")
            ];
            $rowHeight = max($rowHeight, count($cellLines[array_key_last($cellLines)]["lines"]));
            $column += $span;
        }
        for ($lineIndex = 0; $lineIndex < $rowHeight; $lineIndex++) {
            $parts = [];
            foreach ($cellLines as $cell) {
                $parts[] = " ".$utility->align($cell["lines"][$lineIndex] ?? "", $cell["width"], $cell["align"])." ";
            }
            $join = $hasBorder ? $border[3] : " ";
            $lines[] = ($hasBorder ? $border[3] : "").implode($join, $parts).($hasBorder ? $border[3] : "");
        }
        if ($rowIndex < $rowsCount-1) {
            $middle = $this->tableSeparator($widths, $border, $hasBorder, "middle");
            if ($middle !== "") {
                $lines[] = $middle;
            }
        }
    }

    /**
     * Builds a table border separator line.
     *
     * @param int[]    $widths
     * @param string[] $border
     * @param bool $hasBorder
     * @param string $position
     * @return string
     */
    private function tableSeparator(array $widths, array $border, bool $hasBorder, string $position): string
    {
        if (!$hasBorder) {
            return "";
        }
        [$left, $join, $right] = match ($position) {
            "top" => [$border[0], $border[1], $border[2]],
            "bottom" => [$border[8], $border[9], $border[10]],
            default => [$border[5], $border[6], $border[7]]
        };
        return $left.implode($join, array_map(fn (int $width) => str_repeat($border[4], $width+2), $widths)).$right;
    }
}
