<?php

namespace Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Rendering\Renderer\Utilities\ContextAware;
use Lucinda\Console\Rendering\Renderer\Utilities\TextLayout;

final class ParagraphRenderer extends ContextAware
{
    public function render(ElementNode $element, int $width): array
    {
        $utility = new TextLayout($this->context);

        $style = $this->context->getStyles()->resolve($element);
        $indent = max(0, (int) $style->get("indent", 0));
        $padding = max(0, (int) $style->get("padding", 0));
        $contentWidth = max(1, $width-$indent-2*$padding);
        $inlineRenderer = new InlineRenderer($this->context);
        $lines = $inlineRenderer->render($element->getChildren(), $contentWidth, $style, $element->getName() === "code");
        $align = (string) $style->get("align", "left");
        foreach ($lines as &$line) {
            $line = str_repeat(" ", $indent+$padding)
                .($align === "left" ? $line : $utility->align($line, $contentWidth, $align))
                .str_repeat(" ", $padding);
        }
        if ($padding > 0) {
            $blank = str_repeat(" ", min($width, $indent+2*$padding+$contentWidth));
            $lines = array_merge(array_fill(0, $padding, $blank), $lines, array_fill(0, $padding, $blank));
        }
        return $lines;
    }
}