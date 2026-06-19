<?php

namespace Lucinda\Console\Language;

/**
 * Identifies a one-based line and column in the source markup.
 */
final class SourcePosition
{
    private int $line;
    private int $column;

    /**
     * Creates a position value object.
     *
     * @param int $line
     * @param int $column
     * @return void
     */
    public function __construct(
        int $line,
        int $column
    ) {
        $this->line = $line;
        $this->column = $column;
    }

    /**
     * Returns the one-based line number.
     *
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Returns the one-based column number.
     *
     * @return int
     */
    public function getColumn(): int
    {
        return $this->column;
    }
}
