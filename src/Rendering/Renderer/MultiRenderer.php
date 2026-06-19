<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\TextNode;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\WidthResolver;
use Lucinda\Console\Styling\Style;

/**
 * Dispatches block and inline nodes to specialized renderers.
 */
final class MultiRenderer extends ContextAware implements NodesRenderer
{
    private const INLINE_ELEMENTS = [
        "span", "strong", "b", "em", "i", "u", "s", "code", "kbd", "badge", "link", "br"
    ];

    /**
     * Renders a node list into terminal lines.
     *
     * @param \Lucinda\Console\Language\Node[] $nodes
     *
     * @return string[]
     * @param int $width
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function render(array $nodes, int $width): array
    {
        $lines = [];
        $inlineRenderer = new InlineRenderer($this->context);
        foreach ($nodes as $node) {
            if ($node instanceof TextNode) {
                if (trim($node->getValue()) !== "") {
                    $this->appendBlock($lines, $inlineRenderer->render([$node], $width, new Style()));
                }
                continue;
            }
            if (!$node instanceof ElementNode || ($node->getName() === "show" && !$this->isVisible($node, $width))) {
                continue;
            }
            $nodeStyle = $this->context->getStyles()->resolve($node);
            $block = $this->renderElement($node, $width);
            $margin = (int) $nodeStyle->get("margin", 0);
            $this->appendBlock($lines, $block, (int) $nodeStyle->get("margin-top", $margin));
            $marginBottom = (int) $nodeStyle->get("margin-bottom", $margin);
            for ($i = 0; $i < $marginBottom; $i++) {
                $lines[] = "";
            }
        }
        return $lines;
    }

    /**
     * Renders one element using the renderer that matches its tag name.
     *
     * @return string[]
     * @param ElementNode $element
     * @param int $availableWidth
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderElement(ElementNode $element, int $availableWidth): array
    {
        $utility = new WidthResolver($this->context);

        $width = $utility->resolveWidth($element, $availableWidth);
        return match ($element->getName()) {
            "view", "section", "show" => $this->render($element->getChildren(), $width),
            "h1", "h2", "h3", "h4", "h5", "h6", "p", "success", "info", "warning", "error" =>
                $this->renderParagraph($element, $width),
            "box" => $this->renderBox($element, $width),
            "columns" => $this->renderColumns($element, $width),
            "table" => $this->renderTable($element, $width),
            "ol", "ul" => $this->renderList($element, $width),
            "hr" => [str_repeat($this->context->getEnvironment()->getUnicode() ? "\u{2500}" : "-", $width)],
            "spacer" => array_fill(0, max(1, (int) $element->getAttribute("lines", "1")), ""),
            "progress" => [$this->renderProgress($element, $width)],
            "spinner" => [$this->renderSpinner($element)],
            default => in_array($element->getName(), self::INLINE_ELEMENTS, true)
                ? $this->renderInline([$element], $width, new Style())
                : []
        };
    }

    /**
     * Renders a paragraph-like element.
     *
     * @return string[]
     * @param ElementNode $element
     * @param int $width
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderParagraph(ElementNode $element, int $width): array
    {
        $renderer = new ParagraphRenderer($this->context);
        return $renderer->render($element, $width);
    }

    /**
     * Renders a box element.
     *
     * @return string[]
     * @param ElementNode $element
     * @param int $width
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderBox(ElementNode $element, int $width): array
    {
        $renderer = new BoxRenderer($this->context);
        return $renderer->render($element, $width, $this);
    }

    /**
     * Renders a columns element.
     *
     * @return string[]
     * @param ElementNode $element
     * @param int $width
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderColumns(ElementNode $element, int $width): array
    {
        $renderer = new ColumnsRenderer($this->context);
        return $renderer->render($element, $width, $this);
    }

    /**
     * Renders a table element.
     *
     * @return string[]
     * @param ElementNode $table
     * @param int $width
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderTable(ElementNode $table, int $width): array
    {
        $renderer = new TableRenderer($this->context);
        return $renderer->render($table, $width);
    }

    /**
     * Renders an ordered or unordered list element.
     *
     * @return string[]
     * @param ElementNode $list
     * @param int $width
     * @param int $depth
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderList(ElementNode $list, int $width, int $depth = 0): array
    {
        $renderer = new ListRenderer($this->context);
        return $renderer->render($list, $width, $depth);
    }

    /**
     * Renders a progress element as a single line.
     *
     * @param ElementNode $element
     * @param int $width
     * @return string
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderProgress(ElementNode $element, int $width): string
    {
        $renderer = new ProgressRenderer($this->context);
        return $renderer->render($element, $width);
    }

    /**
     * Renders a spinner element as a single frame.
     *
     * @param ElementNode $element
     * @return string
     */
    private function renderSpinner(ElementNode $element): string
    {
        $frame = (string) $element->getAttribute("frame", $this->context->getEnvironment()->getUnicode() ? "\u{280B}" : "|");
        $label = trim((string) $element->getAttribute("label", ""));
        return $frame.($label === "" ? "" : " ".$label);
    }

    /**
     * Renders inline nodes through the inline renderer.
     *
     * @param \Lucinda\Console\Language\Node[] $nodes
     *
     * @return string[]
     * @param int $width
     * @param Style $style
     * @param bool $preserveWhitespace
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function renderInline(array $nodes, int $width, Style $style, bool $preserveWhitespace = false): array
    {
        $renderer = new InlineRenderer($this->context);
        return $renderer->render($nodes, $width, $style, $preserveWhitespace);
    }

    /**
     * Determines whether a responsive show element should render at the current width.
     *
     * @param ElementNode $element
     * @param int $width
     * @return bool
     */
    private function isVisible(ElementNode $element, int $width): bool
    {
        $min = (int) $element->getAttribute("min-width", "0");
        $max = (int) $element->getAttribute("max-width", (string) PHP_INT_MAX);
        return $width >= $min && $width <= $max;
    }

    /**
     * Appends a rendered block with optional top margin.
     *
     * @param string[] $target
     * @param string[] $block
     * @param int $marginTop
     * @return void
     */
    private function appendBlock(array &$target, array $block, int $marginTop = 0): void
    {
        if ($block === []) {
            return;
        }
        for ($i = 0; $i < $marginTop; $i++) {
            $target[] = "";
        }
        array_push($target, ...$block);
    }
}
