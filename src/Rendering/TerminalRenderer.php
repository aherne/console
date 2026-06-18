<?php

namespace Lucinda\Console\Rendering;

use Lucinda\Console\Language\DocumentNode;
use Lucinda\Console\Rendering\Renderer\MultiRenderer;
use Lucinda\Console\Rendering\Renderer\Utilities\RenderingContext;
use Lucinda\Console\Styling\StyleResolver;
use Lucinda\Console\Styling\Theme;
use Lucinda\Console\Terminal\ColorDepth;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

final class TerminalRenderer
{
    private StyleResolver $styles;
    private Results $environment;
    private DisplayWidth $displayWidth;
    private AnsiPainter $ansiPainter;

    public function __construct(private readonly bool $ansi)
    {
        $this->displayWidth = new DisplayWidth();
        $this->ansiPainter = new AnsiPainter(new Color());
    }

    public function render(DocumentNode $document, Results $environment, Theme $theme): string
    {
        $this->environment = $environment;
        if (!$this->ansi) {
            $this->environment = clone $environment;
            $this->environment->setColorDepth(ColorDepth::NONE);
            $this->environment->setHyperlinks(false);
            $this->environment->setInteractive(false);
        }
        $this->styles = new StyleResolver($theme);
        $renderer = new MultiRenderer($this->getRenderingContext());
        return rtrim(implode("\n", $renderer->render($document->getChildren(), $this->environment->getWidth())), "\n");
    }

    private function getRenderingContext(): RenderingContext
    {
        return new RenderingContext(
            $this->styles,
            $this->displayWidth,
            $this->environment,
            $this->ansiPainter,
            $this->ansi
        );
    }
}
