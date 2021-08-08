<?php
namespace Lucinda\Console;

/**
 * Encapsulates a list of options to be displayed in bash console / windows terminal
 */
abstract class AbstractList implements Stringable
{
    public const INDENT_INCREMENT = 5;
    protected $indent;
    protected $caption;
    protected $items = [];

    /**
     * Initializes list by setting textual caption and optional indentation
     *
     * @param string|Text $caption
     * @param int $indent
     */
    public function __construct($caption=null, int $indent = 0)
    {
        if (!($caption===null || $caption instanceof Text || is_string($caption))) {
            throw new Exception("Invalid caption type");
        }
        $this->caption = $caption;
        $this->indent = $indent+self::INDENT_INCREMENT;
    }

    /**
     * Adds textual item to list
     *
     * @param string|Text $item
     */
    public function addItem($item): void
    {
        if (!($item instanceof Text || is_string($item))) {
            throw new Exception("Invalid item type");
        }
        $this->items[] = $item;
    }

    /**
     * Adds sublist to list and returns it to be set
     *
     * @param string|Text $caption
     * @return AbstractList
     */
    public function addList($caption=null): AbstractList
    {
        $class = get_class($this);
        $item = new $class($caption, $this->indent);
        $this->items[] = $item;
        return $item;
    }

    /**
     * Formats list option number for later display
     *
     * @param int $optionNumber
     * @return string
     */
    abstract protected function formatOptionNumber(int $optionNumber): string;

    /**
     * {@inheritDoc}
     * @see \Lucinda\Console\Stringable::toString()
     */
    public function toString(): string
    {
        $output = "";

        // add caption
        if ($this->caption) {
            if ($this->caption instanceof Text) {
                $output .= $this->caption->toString();
            } else {
                $output .= $this->caption;
            }
            $output .= "\n";
        }

        // adds items
        foreach ($this->items as $i=>$item) {
            $line = str_repeat(" ", $this->indent).$this->formatOptionNumber($i+1)." ";
            if ($item instanceof Stringable) {
                $line .= $item->toString();
            } else {
                $line .= $item;
            }
            $output .= $line."\n";
        }
        return substr($output, 0, -1);
    }
}
