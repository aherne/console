<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\Node;
use Lucinda\Console\Language\TextNode;
use Lucinda\Console\Rendering\InlineSegment;
use Lucinda\Console\Styling\Style;

/**
 * Low-level inline text collection, wrapping, segment merging, and painting utilities.
 */
final class InlineLayout extends ContextAware
{
    /**
     * Renders segments as one clipped or ellipsized line.
     *
     * @param InlineSegment[] $segments
     * @param int $width
     * @param string $overflow
     * @return string
     */
    public function renderSingleLine(array $segments, int $width, string $overflow): string
    {
        $limit = $overflow === "ellipsis" ? max(0, $width-1) : $width;
        $result = [];
        $used = 0;
        $truncated = false;
        foreach ($segments as $segment) {
            foreach (mb_str_split(str_replace(["\r", "\n", "\t"], " ", $segment->getText()), 1, "UTF-8") as $character) {
                $characterWidth = $this->context->getDisplayWidth()->get($character);
                if ($used+$characterWidth > $limit) {
                    $truncated = true;
                    break 2;
                }
                $this->appendSegment($result, new InlineSegment($character, $segment->getStyle(), $segment->getHref()));
                $used += $characterWidth;
            }
        }
        if ($truncated && $overflow === "ellipsis") {
            $this->appendSegment($result, new InlineSegment($this->context->getEnvironment()->getUnicode() ? "\u{2026}" : ".", new Style()));
        }
        return $this->paintSegments($result);
    }

    /**
     * Wraps segments by display character width.
     *
     * @param InlineSegment[] $segments
     *
     * @return string[]
     * @param int $width
     */
    public function renderCharacterWrapped(array $segments, int $width): array
    {
        $lines = [[]];
        $used = 0;
        foreach ($segments as $segment) {
            foreach (mb_str_split(str_replace(["\r", "\t"], " ", $segment->getText()), 1, "UTF-8") as $character) {
                if ($character === "\n") {
                    $lines[] = [];
                    $used = 0;
                    continue;
                }
                $characterWidth = $this->context->getDisplayWidth()->get($character);
                if ($used > 0 && $used+$characterWidth > $width) {
                    $lines[] = [];
                    $used = 0;
                }
                $this->appendSegment($lines[array_key_last($lines)], new InlineSegment($character, $segment->getStyle(), $segment->getHref()));
                $used += $characterWidth;
            }
        }
        return array_map(fn (array $line): string => rtrim($this->paintSegments($line)), $lines);
    }

    /**
     * Paints already-laid-out inline segments into terminal text.
     *
     * @param InlineSegment[] $segments
     * @return string
     */
    public function paintSegments(array $segments): string
    {
        $output = "";
        foreach ($segments as $segment) {
            $text = $this->context->getDisplayWidth()->escape($segment->getText());
            if ($this->context->getAnsi()) {
                $output .= $this->context->getAnsiPainter()->paint(
                    $text,
                    $segment->getStyle(),
                    $this->context->getEnvironment(), $segment->getHref()
                    );
            } else {
                $output .= $text;
                if ($segment->getHref() !== null) {
                    $output .= " (".$segment->getHref().")";
                }
            }
        }
        return $output;
    }

    /**
     * Flattens inline nodes into styled text segments.
     *
     * @param Node[]          $nodes
     * @param InlineSegment[] $segments
     * @param Style $style
     * @param ?string $href
     * @param bool $preserveWhitespace
     * @return void
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function collectSegments(array $nodes, Style $style, array &$segments, ?string $href, bool $preserveWhitespace): void
    {
        $isAnsi = $this->context->getAnsi();
        foreach ($nodes as $node) {
            if ($node instanceof TextNode) {
                $segments[] = new InlineSegment($node->getValue(), $style, $href);
                continue;
            }
            if (!$node instanceof ElementNode) {
                continue;
            }
            if ($node->getName() === "br") {
                $segments[] = new InlineSegment("\n", $style, $href);
                continue;
            }
            $childStyle = $this->context->getStyles()->resolve($node, $style);
            if ($node->getName() === "link") {
                $url = (string) $node->getAttribute("href", "");
                $this->collectSegments($node->getChildren(), $childStyle, $segments, $isAnsi ? $url : null, $preserveWhitespace);
                if (!$isAnsi) {
                    $segments[] = new InlineSegment(" (".$url.")", $childStyle);
                }
                continue;
            }
            $this->collectSegments($node->getChildren(), $childStyle, $segments, $href, $preserveWhitespace || $node->getName() === "code");
        }
    }

    /**
     * Appends a segment to a line, merging adjacent compatible segments.
     *
     * @param InlineSegment[] $line
     * @param InlineSegment $segment
     * @return void
     */
    public function appendSegment(array &$line, InlineSegment $segment): void
    {
        $last = array_key_last($line);
        if ($last !== null && $line[$last]->getStyle()->all() === $segment->getStyle()->all() && $line[$last]->getHref() === $segment->getHref()) {
            $line[$last] = new InlineSegment($line[$last]->getText().$segment->getText(), $segment->getStyle(), $segment->getHref());
        } else {
            $line[] = $segment;
        }
    }

    /**
     * Splits a string into chunks no wider than the requested display width.
     *
     * @return string[]
     * @param string $value
     * @param int $width
     */
    public function splitToWidth(string $value, int $width): array
    {
        if ($this->context->getDisplayWidth()->get($value) <= $width) {
            return [$value];
        }
        $chunks = [];
        $chunk = "";
        $chunkWidth = 0;
        foreach (mb_str_split($value, 1, "UTF-8") as $character) {
            $characterWidth = $this->context->getDisplayWidth()->get($character);
            if ($chunk !== "" && $chunkWidth+$characterWidth > $width) {
                $chunks[] = $chunk;
                $chunk = "";
                $chunkWidth = 0;
            }
            $chunk .= $character;
            $chunkWidth += $characterWidth;
        }
        if ($chunk !== "") {
            $chunks[] = $chunk;
        }
        return $chunks;
    }
}
