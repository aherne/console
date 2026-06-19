<?php

namespace Lucinda\Console;

use Lucinda\Console\Terminal\EnvironmentDetector;

/**
 * Convenience object that renders markup immediately for the current terminal.
 */
final class Wrapper
{
    private string $body;

    /**
     * Compiles markup with environment settings detected from the active terminal.
     *
     * @param string $markup
     * @param RenderMode $mode
     * @return void
     * @throws \Lucinda\Console\Exception
     */
    public function __construct(
        string $markup,
        RenderMode $mode = RenderMode::ANSI
    ) {
        $detector = new EnvironmentDetector();
        $engine = new Engine();
        $this->body = $engine->render($markup, $detector->getResults(), $mode);
    }

    /**
     * Returns the rendered terminal output.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Writes the rendered body to standard output.
     *
     * @return void
     */
    public function display(): void
    {
        echo $this->body;
    }
}
