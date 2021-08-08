<?php
namespace Lucinda\Console;

/**
 * Encapsulates an ordered list of options to be displayed in bash console / windows terminal
 */
class OrderedList extends AbstractList
{
    /**
     * {@inheritDoc}
     * @see \Lucinda\Console\AbstractList::formatOptionNumber()
     */
    protected function formatOptionNumber(int $optionNumber): string
    {
        return $optionNumber.":";
    }
}
