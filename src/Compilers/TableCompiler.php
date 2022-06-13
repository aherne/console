<?php

namespace Lucinda\Console\Compilers;

use Lucinda\Console\Exception;
use Lucinda\Console\Table;
use Lucinda\Console\Text;

/**
 * Compiles body of text for <table> tag references
 */
class TableCompiler extends AbstractCompiler
{
    /**
     * {@inheritDoc}
     *
     * @see \Lucinda\Console\Compilers\AbstractCompiler::compile()
     */
    protected function compile(string $html): string
    {
        return preg_replace_callback(
            "/<table>(.+?)<\/table>/mis",
            function ($matches) {
                $tmp = $matches[1];

                // parse thead to get columns
                $columns = $this->getColumns($tmp);

                // parse tbody to get rows
                $rows = $this->getRows($tmp, $columns);

                // compose table and return
                $table = new Table($columns);
                foreach ($rows as $row) {
                    $table->addRow($row);
                }
                return $table->__toString();
            },
            $html
        );
    }

    /**
     * Gets columns referenced in <thead> contents
     *
     * @param  string $table
     * @return array<string|Text>
     * @throws Exception
     */
    private function getColumns(string $table): array
    {
        $columns = [];

        $matches2 = [];
        preg_match("/<thead>(.+?)<\/thead>/mis", $table, $matches2);
        if (empty($matches2)) {
            throw new Exception("Table missing thead");
        }
        $matches3 = [];
        preg_match("/<tr>(.+?)<\/tr>/mis", $matches2[1], $matches3);
        if (empty($matches3)) {
            throw new Exception("Table missing thead > tr");
        }
        $matches4 = [];
        preg_match_all("/<td(\s+style\s*=\s*\"([^\"]+)\")?>(.+?)<\/td>/", $matches3[1], $matches4);
        if (sizeof($matches4[3])<2) {
            throw new Exception("Table must have a minimum of two td");
        }
        foreach ($matches4[3] as $i=>$text) {
            $column = $this->getText($text, $matches4[2][$i]);
            $columns[] = ($this->isWindows ? $column->getOriginalValue() : $column);
        }

        return $columns;
    }

    /**
     * Gets rows referenced in <tbody> contents
     *
     * @param  string             $table
     * @param  array<string|Text> $columns
     * @throws Exception
     * @return string[][]|Text[][]
     */
    private function getRows(string $table, array $columns): array
    {
        $rows = [];
        $matches2 = [];
        preg_match("/<tbody>(.+?)<\/tbody>/mis", $table, $matches2);
        if (empty($matches2)) {
            throw new Exception("Table missing tbody");
        }
        $matches3 = [];
        preg_match_all("/<tr>(.+?)<\/tr>/mis", $matches2[1], $matches3);
        if (empty($matches3)) {
            throw new Exception("Table missing tbody > tr");
        }
        foreach ($matches3[1] as $item) {
            $row = [];
            $matches4 = [];
            preg_match_all("/<td(\s+style\s*=\s*\"([^\"]+)\")?>(.+?)<\/td>/", $item, $matches4);
            if (sizeof($matches4[3])!=sizeof($columns)) {
                throw new Exception("Row column number doesn't match that in thead");
            }
            foreach ($matches4[3] as $i=>$text) {
                $value = $this->getText($text, $matches4[2][$i]);
                $row[] = ($this->isWindows ? $value->getOriginalValue() : $value);
            }
            $rows[] = $row;
        }
        return $rows;
    }
}
