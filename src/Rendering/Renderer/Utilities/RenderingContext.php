<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\AnsiPainter;
use Lucinda\Console\Rendering\DisplayWidth;
use Lucinda\Console\Styling\StyleResolver;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

final class RenderingContext
{
    private StyleResolver $styles;
    private DisplayWidth $displayWidth;
    private Results $environment;
    private AnsiPainter $ansiPainter;
    private bool $ansi;

    public function __construct(
        StyleResolver $styles,
        DisplayWidth $displayWidth,
        Results $environment,
        AnsiPainter $ansiPainter,
        bool $ansi
    )
    {
        $this->styles = $styles;
        $this->displayWidth = $displayWidth;
        $this->environment = $environment;
        $this->ansiPainter = $ansiPainter;
        $this->ansi = $ansi;
    }

    public function getStyles(): StyleResolver
    {
        return $this->styles;
    }

    public function getDisplayWidth(): DisplayWidth
    {
        return $this->displayWidth;
    }

    public function getEnvironment(): Results
    {
        return $this->environment;
    }

    public function getAnsiPainter(): AnsiPainter
    {
        return $this->ansiPainter;
    }

    public function getAnsi(): bool
    {
        return $this->ansi;
    }
}