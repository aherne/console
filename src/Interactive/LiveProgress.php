<?php

namespace Lucinda\Console\Interactive;

use Lucinda\Console\Engine;
use Lucinda\Console\Exception;
use Lucinda\Console\RenderMode;
use Lucinda\Console\Terminal\EnvironmentDetector;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

/**
 * Renders an updating progress bar on one interactive terminal line.
 */
final class LiveProgress
{
    /**
     * Creates a live progress renderer and verifies terminal interactivity.
     *
     * @param Engine $engine
     * @param ?Results $environment
     * @param float $max
     * @return void
     * @throws \Lucinda\Console\Exception
     */
    public function __construct(
        private readonly Engine $engine,
        private readonly ?Results $environment = null,
        private readonly float $max = 100
    ) {
        $environment = $this->getEnvironment();
        if (!$environment->getInteractive()) {
            throw new Exception("Live progress requires an interactive terminal");
        }
    }

    /**
     * Repaints the current progress value and optional label.
     *
     * @param float $value
     * @param ?string $label
     * @return void
     * @throws \Lucinda\Console\Exception
     */
    public function update(float $value, ?string $label = null): void
    {
        $markup = '<progress value="'.$value.'" max="'.$this->max.'"'
            .($label === null ? "" : ' label="'.htmlspecialchars($label, ENT_QUOTES).'"').'/>';
        $line = $this->engine->render($markup, $this->getEnvironment(), RenderMode::ANSI);
        fwrite(STDOUT, "\r\e[2K".$line);
    }

    /**
     * Paints the final progress state and moves to the next line.
     *
     * @param ?float $value
     * @param ?string $label
     * @return void
     * @throws \Lucinda\Console\Exception
     */
    public function finish(?float $value = null, ?string $label = null): void
    {
        $this->update($value ?? $this->max, $label);
        fwrite(STDOUT, PHP_EOL);
    }

    /**
     * Returns the supplied environment or detects the active terminal.
     *
     * @return Results
     */
    private function getEnvironment(): Results
    {
        return $this->environment ?? (new EnvironmentDetector())->getResults();
    }
}
