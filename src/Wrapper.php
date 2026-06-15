<?php

namespace Lucinda\Console;

use Lucinda\Console\Compilers\DivCompiler;
use Lucinda\Console\Compilers\TableCompiler;
use Lucinda\Console\Compilers\ListCompiler;

/**
 * Parses a pseudo-HTML and builds a console text ready for display
 */
class Wrapper
{
    private string $body;
    private bool $isWindows;

    /**
     * Parses pseudo-HTML received, taking into account if platform has styling abilities
     *
     * @param string    $body            Body containing pseudo-html
     * @param bool|null $supportsStyling Explicit ANSI styling support, or null to detect it
     */
    public function __construct(string $body, ?bool $supportsStyling = null)
    {
        $this->isWindows = !($supportsStyling ?? $this->supportsStyling());

        $this->setBody($body);
    }

    /**
     * Detects whether standard output supports ANSI styling
     *
     * @return bool
     */
    private function supportsStyling(): bool
    {
        if (getenv("NO_COLOR") !== false || getenv("TERM") === "dumb") {
            return false;
        }

        if (!defined("STDOUT")) {
            return false;
        }

        $isTerminal = function_exists("stream_isatty")
            ? stream_isatty(STDOUT)
            : (function_exists("posix_isatty") && posix_isatty(STDOUT));
        if (!$isTerminal) {
            return false;
        }

        if (PHP_OS_FAMILY === "Windows") {
            return function_exists("sapi_windows_vt100_support")
                && sapi_windows_vt100_support(STDOUT);
        }

        return true;
    }

    /**
     * Sets body of text to be displayed
     *
     * @param string $body Body containing pseudo-html
     */
    private function setBody(string $body): void
    {
        $divCompiler = new DivCompiler($body, $this->isWindows);
        $body = $divCompiler->getBody();

        $tableCompiler = new TableCompiler($body, $this->isWindows);
        $body = $tableCompiler->getBody();

        $listCompiler = new ListCompiler($body, $this->isWindows);
        $body = $listCompiler->getBody();

        $this->body = $body;
    }

    /**
     * Gets payload to be displayed back to users
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
