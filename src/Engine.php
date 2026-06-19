<?php

namespace Lucinda\Console;

use Lucinda\Console\Language\DocumentNode;
use Lucinda\Console\Language\Parser;
use Lucinda\Console\Rendering\TerminalRenderer;
use Lucinda\Console\Styling\Theme;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

/**
 * Coordinates parsing markup and rendering it for a terminal environment.
 */
final class Engine
{
    /**
     * Creates an engine with the parser and theme used for subsequent renders.
     *
     * @param Parser $parser
     * @param Theme $theme
     * @return void
     */
    public function __construct(
        private readonly Parser $parser = new Parser(),
        private readonly Theme $theme = new Theme()
    ) {
    }

    /**
     * Parses console markup into a validated document tree.
     *
     * @param string $source
     * @return DocumentNode
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function compile(string $source): DocumentNode
    {
        return $this->parser->parse($source);
    }

    /**
     * Renders markup or a precompiled document using the requested output mode.
     *
     * @param string|DocumentNode $source
     * @param Results $environment
     * @param RenderMode $mode
     * @return string
     * @throws \Lucinda\Console\Exception
     */
    public function render(
        string|DocumentNode $source,
        Results $environment,
        RenderMode $mode = RenderMode::ANSI
    ): string {
        $document = is_string($source) ? $this->compile($source) : $source;
        return $this->renderer($mode)->render($document, $environment, $this->theme);
    }

    /**
     * Builds the terminal renderer appropriate for the selected mode.
     *
     * @param RenderMode $mode
     * @return TerminalRenderer
     */
    private function renderer(RenderMode $mode): TerminalRenderer
    {
        return match ($mode) {
            RenderMode::ANSI => new TerminalRenderer(true),
            RenderMode::PLAIN_TEXT => new TerminalRenderer(false)
        };
    }
}
