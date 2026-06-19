<?php

namespace Lucinda\Console\Interactive;

use Lucinda\Console\Exception;
use Lucinda\Console\Rendering\DisplayWidth;
use Lucinda\Console\Terminal\EnvironmentDetector;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

/**
 * Renders an animated spinner on one interactive terminal line.
 */
final class LiveSpinner
{
    private int $frame = 0;
    private DisplayWidth $displayWidth;

    /**
     * Creates a live spinner and verifies terminal interactivity.
     *
     * @param ?Results $environment
     * @param string $label
     * @return void
     * @throws \Lucinda\Console\Exception
     */
    public function __construct(
        private readonly ?Results $environment = null,
        private readonly string $label = "Loading"
    ) {
        $this->displayWidth = new DisplayWidth();
        $environment = $this->getEnvironment();
        if (!$environment->getInteractive()) {
            throw new Exception("Live spinner requires an interactive terminal");
        }
    }

    /**
     * Advances and redraws the spinner by one frame.
     *
     * @return void
     */
    public function tick(): void
    {
        $environment = $this->getEnvironment();
        $frames = $environment->getUnicode()
            ? ["\u{280B}", "\u{2819}", "\u{2839}", "\u{2838}", "\u{283C}", "\u{2834}", "\u{2826}", "\u{2827}", "\u{2807}", "\u{280F}"]
            : ["|", "/", "-", "\\"];
        fwrite(STDOUT, "\r\e[2K".$frames[$this->frame%count($frames)]." ".$this->displayWidth->escape($this->label));
        $this->frame++;
    }

    /**
     * Clears the spinner line and writes the completion message.
     *
     * @param string $message
     * @return void
     */
    public function finish(string $message = "Done"): void
    {
        fwrite(STDOUT, "\r\e[2K".$this->displayWidth->escape($message).PHP_EOL);
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
