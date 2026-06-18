<?php

namespace Lucinda\Console;

use Lucinda\Console\Language\DocumentNode;
use Lucinda\Console\Language\Parser;
use Lucinda\Console\Rendering\TerminalRenderer;
use Lucinda\Console\Styling\Theme;
use Lucinda\Console\Terminal\EnvironmentDetector;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

final class Engine
{
    public function __construct(
        private readonly Parser $parser = new Parser(),
        private readonly Theme $theme = new Theme()
    ) {
    }

    public function compile(string $source): DocumentNode
    {
        return $this->parser->parse($source);
    }

    public function render(
        string|DocumentNode $source,
        Results $environment,
        RenderMode $mode = RenderMode::ANSI
    ): string {
        $document = is_string($source) ? $this->compile($source) : $source;
        return $this->renderer($mode)->render($document, $environment, $this->theme);
    }

    private function renderer(RenderMode $mode): TerminalRenderer
    {
        return match ($mode) {
            RenderMode::ANSI => new TerminalRenderer(true),
            RenderMode::PLAIN_TEXT => new TerminalRenderer(false)
        };
    }
}
