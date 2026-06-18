<?php

namespace Lucinda\Console\Language;

final class SourcePosition
{
    private int $line;
    private int $column;

    public function __construct(
        int $line,
        int $column
    ) {
        $this->line = $line;
        $this->column = $column;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}
