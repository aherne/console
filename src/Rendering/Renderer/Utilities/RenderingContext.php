<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\AnsiPainter;
use Lucinda\Console\Rendering\DisplayWidth;
use Lucinda\Console\Styling\StyleResolver;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

/**
 * Bundles shared services and flags used throughout rendering.
 */
final class RenderingContext
{
    private StyleResolver $styles;
    private DisplayWidth $displayWidth;
    private Results $environment;
    private AnsiPainter $ansiPainter;
    private bool $ansi;

    /**
     * Creates a rendering context from resolved terminal and styling services.
     *
     * @param StyleResolver $styles
     * @param DisplayWidth $displayWidth
     * @param Results $environment
     * @param AnsiPainter $ansiPainter
     * @param bool $ansi
     * @return void
     */
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

    /**
     * Returns the style resolver.
     *
     * @return StyleResolver
     */
    public function getStyles(): StyleResolver
    {
        return $this->styles;
    }

    /**
     * Returns the display width calculator.
     *
     * @return DisplayWidth
     */
    public function getDisplayWidth(): DisplayWidth
    {
        return $this->displayWidth;
    }

    /**
     * Returns terminal environment capabilities.
     *
     * @return Results
     */
    public function getEnvironment(): Results
    {
        return $this->environment;
    }

    /**
     * Returns the ANSI painter.
     *
     * @return AnsiPainter
     */
    public function getAnsiPainter(): AnsiPainter
    {
        return $this->ansiPainter;
    }

    /**
     * Returns whether ANSI output is enabled.
     *
     * @return bool
     */
    public function getAnsi(): bool
    {
        return $this->ansi;
    }
}
