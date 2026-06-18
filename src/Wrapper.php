<?php

namespace Lucinda\Console;

use Lucinda\Console\Terminal\EnvironmentDetector;

final class Wrapper
{
    private string $body;

    public function __construct(
        string $markup,
        RenderMode $mode = RenderMode::ANSI
    ) {
        $detector = new EnvironmentDetector();
        $engine = new Engine();
        $this->body = $engine->render($markup, $detector->getResults(), $mode);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function display(): void
    {
        echo $this->body;
    }
}
