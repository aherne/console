<?php

namespace Lucinda\Console\Rendering;

final class DisplayWidth
{
    public function get(string $value): int
    {
        return mb_strwidth($this->stripControlSequences($value), "UTF-8");
    }

    public function stripControlSequences(string $value): string
    {
        $value = preg_replace('~\x1B\][^\x07]*(?:\x07|\x1B\\\\)~', "", $value) ?? $value;
        return preg_replace('#\x1B\[[0-?]*[ -/]*[@-~]#', "", $value) ?? $value;
    }

    public function escape(string $value): string
    {
        $value = $this->stripControlSequences($value);
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', "", $value) ?? "";
    }
}
