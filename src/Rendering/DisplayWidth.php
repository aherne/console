<?php

namespace Lucinda\Console\Rendering;

/**
 * Measures visible terminal width while ignoring control sequences.
 */
final class DisplayWidth
{
    /**
     * Returns the display width of visible text.
     *
     * @param string $value
     * @return int
     */
    public function get(string $value): int
    {
        return mb_strwidth($this->stripControlSequences($value), "UTF-8");
    }

    /**
     * Removes ANSI CSI and OSC control sequences from text.
     *
     * @param string $value
     * @return string
     */
    public function stripControlSequences(string $value): string
    {
        $value = preg_replace('~\x1B\][^\x07]*(?:\x07|\x1B\\\\)~', "", $value) ?? $value;
        return preg_replace('#\x1B\[[0-?]*[ -/]*[@-~]#', "", $value) ?? $value;
    }

    /**
     * Removes control sequences and non-printable control characters.
     *
     * @param string $value
     * @return string
     */
    public function escape(string $value): string
    {
        $value = $this->stripControlSequences($value);
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', "", $value) ?? "";
    }
}
