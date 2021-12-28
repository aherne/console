<?php
namespace Lucinda\Console;

/**
 * Encapsulates a table to be displayed in bash console / windows terminal
 */
class Table implements \Stringable
{
    private array $columns = [];
    private array $rows = [];
    private array $colors = [];

    /**
     * Sets table columns
     *
     * @param string|Text[] $columns
     * @throws \Exception
     */
    public function __construct(array $columns)
    {
        if (empty($columns)) {
            throw new \Exception("Columns cannot be empty!");
        }
        $this->columns = $columns;
    }

    /**
     * Adds row to table
     *
     * @param string|Text[] $row
     * @throws \Exception
     */
    public function addRow(array $row): void
    {
        if (sizeof($row)!=sizeof($this->columns)) {
            throw new \Exception("Invalid size of rows!");
        }
        $this->rows[] = $row;
    }

    /**
     * {@inheritDoc}
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
     * @return array
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
     * @param array $lengths
     * @return array
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
                $line .= $column->getStyledValue().str_repeat(" ", $lengths[$i]-strlen($column->getOriginalValue()))." | ";
            } else {
                $line .= $column.str_repeat(" ", $lengths[$i]-strlen($column))." | ";
            }
        }

        $lines[] = $line;

        // adds row lines
        foreach ($this->rows as $row) {
            $lines[] = str_repeat("-", $emptyLineLength);
            $line = "| ";
            foreach ($row as $i=>$value) {
                if ($value instanceof Text) {
                    $line .= $value->getStyledValue().str_repeat(" ", $lengths[$i]-strlen($value->getOriginalValue()))." | ";
                } else {
                    $line .= $value.str_repeat(" ", $lengths[$i]-strlen($value))." | ";
                }
            }
            $lines[] = $line;
        }
        $lines[] = str_repeat("-", $emptyLineLength);

        return $lines;
    }
}
