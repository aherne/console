<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\InlineSegment;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\InlineLayout;
use Lucinda\Console\Styling\Style;

final class InlineRenderer extends ContextAware
{
    public function render(array $nodes, int $width, Style $style, bool $preserveWhitespace = false): array
    {
        $utility = new InlineLayout($this->context);

        $segments = [];
        $utility->collectSegments($nodes, $style, $segments, null, $preserveWhitespace);
        $wrap = (string) $style->get("wrap", "word");
        $overflow = (string) $style->get("overflow", "wrap");
        if ($wrap === "none" || in_array($overflow, ["clip", "ellipsis"], true)) {
            return [$utility->renderSingleLine($segments, $width, $overflow)];
        }
        if ($wrap === "character") {
            return $utility->renderCharacterWrapped($segments, $width);
        }
        $lines = [[]];
        $lineWidth = 0;
        foreach ($segments as $segment) {
            $text = $preserveWhitespace ? $segment->getText() : preg_replace('/[\t\r ]+/u', " ", $segment->getText());
            $parts = preg_split('/(\n|\s+)/u', $text ?? "", -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
            foreach ($parts as $part) {
                if ($part === "\n") {
                    $lines[] = [];
                    $lineWidth = 0;
                    continue;
                }
                $isSpace = trim($part) === "";
                if ($isSpace) {
                    if ($lineWidth > 0) {
                        $utility->appendSegment($lines[array_key_last($lines)], new InlineSegment(" ", $segment->getStyle(), $segment->getHref()));
                        $lineWidth++;
                    }
                    continue;
                }
                $partWidth = $this->context->getDisplayWidth()->get($part);
                if ($lineWidth > 0 && $lineWidth+$partWidth > $width) {
                    $lines[] = [];
                    $lineWidth = 0;
                }
                foreach ($utility->splitToWidth($part, $width) as $chunkIndex => $chunk) {
                    $chunkWidth = $this->context->getDisplayWidth()->get($chunk);
                    if ($chunkIndex > 0 || ($lineWidth > 0 && $lineWidth+$chunkWidth > $width)) {
                        $lines[] = [];
                        $lineWidth = 0;
                    }
                    $utility->appendSegment($lines[array_key_last($lines)], new InlineSegment($chunk, $segment->getStyle(), $segment->getHref()));
                    $lineWidth += $chunkWidth;
                }
            }
        }

        return array_map(fn (array $line): string => rtrim($utility->paintSegments($line)), $lines);
    }
}