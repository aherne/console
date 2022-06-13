<?php

namespace Lucinda\Console;

/**
 * Encapsulates a unordered list of options to be displayed in bash console / windows terminal
 */
class UnorderedList extends AbstractList
{
    /**
     * {@inheritDoc}
     *
     * @see \Lucinda\Console\AbstractList::formatOptionNumber()
     */
    protected function formatOptionNumber(int $optionNumber): string
    {
        return "-";
    }
}
