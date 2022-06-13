<?php

namespace Lucinda\Console;

/**
 * Encapsulates a table to be displayed in bash console / windows terminal
 */
class Table implements \Stringable
{
    /**
     * @var array<string|Text>
     */
    private array $columns = [];
    /**
     * @var array<array<string|Text>>
     */
    private array $rows = [];

    /**
     * Sets table columns
     *
     * @param  array<string|Text> $columns
     * @throws Exception
     */
    public function __construct(array $columns)
    {
        if (empty($columns)) {
            throw new Exception("Columns cannot be empty!");
        }
        $this->columns = $columns;
    }

    /**
     * Adds row to table
     *
     * @param  array<string|Text> $row
     * @throws Exception
     */
    public function addRow(array $row): void
    {
        if (sizeof($row)!=sizeof($this->columns)) {
            throw new Exception("Invalid size of rows!");
        }
        $this->rows[] = $row;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Stringable::__toString()
     */
    public function __toString(): string
    {
        $lengths = $this->getLengths();
        $lines = $this->getLines($lengths);
        return implode("\n", $lines);
    }

    /**
     * Gets table column lengths
     *
     * @return array<int,int>
     */
    private function getLengths(): array
    {
        $lengths = [];
        foreach ($this->columns as $i=>$column) {
            if ($column instanceof Text) {
                $lengths[$i] = strlen($column->getOriginalValue());
            } else {
                $lengths[$i] = strlen($column);
            }
        }
        foreach ($this->rows as $row) {
            foreach ($row as $i=>$value) {
                if ($value instanceof Text) {
                    if (strlen($value->getOriginalValue()) > $lengths[$i]) {
                        $lengths[$i] = strlen($value->getOriginalValue());
                    }
                } else {
                    if (strlen($value) > $lengths[$i]) {
                        $lengths[$i] = strlen($value);
                    }
                }
            }
        }
        return $lengths;
    }

    /**
     * Gets lines to display
     *
     * @param  array<int,int> $lengths
     * @return string[]
     */
    private function getLines(array $lengths): array
    {
        // compiles line character length
        $emptyLineLength = 2+array_sum($lengths)+(3*sizeof($this->columns)-1);

        // adds first line
        $lines = [];
        $lines[] = str_repeat("-", $emptyLineLength);

        // adds columns line
        $line = "| ";
        foreach ($this->columns as $i=>$column) {
            if ($column instanceof Text) {
                $line .= $column->getStyledValue().$this->getSeparator($lengths[$i], $column->getOriginalValue());
            } else {
                $line .= $column.$this->getSeparator($lengths[$i], $column);
            }
        }

        $lines[] = $line;

        // adds row lines
        foreach ($this->rows as $row) {
            $lines[] = str_repeat("-", $emptyLineLength);
            $line = "| ";
            foreach ($row as $i=>$value) {
                if ($value instanceof Text) {
                    $line .= $value->getStyledValue().$this->getSeparator($lengths[$i], $value->getOriginalValue());
                } else {
                    $line .= $value.$this->getSeparator($lengths[$i], $value);
                }
            }
            $lines[] = $line;
        }
        $lines[] = str_repeat("-", $emptyLineLength);

        return $lines;
    }

    /**
     * Gets column separator
     *
     * @param  int    $lengths
     * @param  string $value
     * @return string
     */
    private function getSeparator(int $lengths, string $value): string
    {
        return str_repeat(" ", $lengths-strlen($value))." | ";
    }
}
