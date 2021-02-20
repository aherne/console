<?php
namespace Lucinda\ConsoleTable;

/**
 * Encapsulates a table to be displayed in bash console
 */
class Table
{
    private $columns = [];
    private $rows = [];
    private $colors = [];
    
    /**
     * Sets table columns
     *
     * @param string[] $columns
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
     * Displays table on console
     */
    public function display(): void
    {
        // get column sizes
        $lengths = [];
        foreach ($this->columns as $i=>$column) {
            $lengths[$i] = strlen($column);
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
        
        // compiles line character length
        $emptyLineLength = 2+array_sum($lengths)+(3*sizeof($this->columns)-1);
        
        // adds first line
        $lines = [];
        $lines[] = str_repeat("-", $emptyLineLength);
        
        // adds columns line
        $line = "| ";
        foreach ($this->columns as $i=>$column) {
            $text = new Text($column);
            $text->setFontStyle(FontStyle::BOLD);
            $line .= $text->getStyledValue().str_repeat(" ", $lengths[$i]-strlen($column))." | ";
        }
        
        $lines[] = $line;
        
        // adds row lines
        foreach ($this->rows as $row) {
            $lines[] = str_repeat("-", $emptyLineLength);
            $line = "| ";
            foreach ($row as $i=>$value) {
                if ($value instanceof Text) {
                    $line.=$value->getStyledValue().str_repeat(" ", $lengths[$i]-strlen($value->getOriginalValue()))." | ";
                } else {
                    $line.=$value.str_repeat(" ", $lengths[$i]-strlen($value))." | ";
                }
            }
            $lines[] = $line;
        }
        $lines[] = str_repeat("-", $emptyLineLength);
        
        // displays lines to console
        echo implode("\n", $lines)."\n";
    }
}
