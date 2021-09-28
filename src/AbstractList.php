<?php
namespace Lucinda\Console;

/**
 * Encapsulates a list of options to be displayed in bash console / windows terminal
 */
abstract class AbstractList implements Stringable
{
    const INDENT_INCREMENT = 5;
    
    protected $indent;
    protected $caption;
    protected $items = [];

    /**
     * Initializes list by setting optional indentation
     *
     * @param int $indent
     */
    public function __construct(int $indent = 0)
    {
        $this->indent = $indent+self::INDENT_INCREMENT;
    }
    
    /**
     * Initializes list by setting textual caption and optional indentation
     *
     * @param string|Text $caption
     * @param int $indent
     */
    public function setCaption($caption): void
    {
        if (!($caption===null || $caption instanceof Text || is_string($caption))) {
            throw new Exception("Invalid caption type");
        }
        $this->caption = $caption;
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
     * Adds sublist to list
     *
     * @param AbstractList $list
     */
    public function addList(AbstractList $list): void
    {
        $list->indent();
        $this->items[] = $list;
    }
    
    /**
     * Indents list further
     */
    public function indent(): void
    {
        $this->indent += self::INDENT_INCREMENT;
        // cascade indentation to children
        foreach($this->items as $item) {
            if ($item instanceof AbstractList) {
                $item->indent();
            }
        }
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
