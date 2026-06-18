<?php

namespace Lucinda\Console\Terminal;

use Lucinda\Console\Terminal\EnvironmentDetector\Results;

final class EnvironmentDetector
{
    private Results $results;

    public function __construct()
    {
        $this->setResults();
    }

    private function setResults(): void
    {
        $interactive = defined("STDOUT") && (
            function_exists("stream_isatty")
                ? stream_isatty(STDOUT)
                : (function_exists("posix_isatty") && posix_isatty(STDOUT))
        );
        $noColor = getenv("NO_COLOR") !== false || getenv("TERM") === "dumb" || !$interactive;
        $colorDepth = ColorDepth::NONE;
        if (!$noColor) {
            $colorTerm = strtolower((string) getenv("COLORTERM"));
            $term = strtolower((string) getenv("TERM"));
            $colorDepth = str_contains($colorTerm, "truecolor") || str_contains($colorTerm, "24bit")
                ? ColorDepth::TRUE_COLOR
                : (str_contains($term, "256color") ? ColorDepth::ANSI256 : ColorDepth::ANSI16);
        }

        $results = new Results();
        $results->setWidth($this->detectDimension("COLUMNS", 80));
        $results->setHeight($this->detectDimension("LINES", 24));
        $results->setColorDepth($colorDepth);
        $results->setUnicode(!in_array(strtolower((string) getenv("TERM")), ["dumb", "cons25"], true));
        $results->setHyperlinks($interactive && $this->detectHyperlinks());
        $results->setInteractive($interactive);
        $this->results = $results;
    }

    public function getResults(): Results
    {
        return $this->results;
    }

    private function detectDimension(string $variable, int $default): int
    {
        $value = filter_var(getenv($variable), FILTER_VALIDATE_INT);
        return is_int($value) && $value > 0 ? $value : $default;
    }

    private function detectHyperlinks(): bool
    {
        return getenv("WT_SESSION") !== false
            || getenv("VTE_VERSION") !== false
            || getenv("TERM_PROGRAM") !== false;
    }
}