<?php
namespace Lucinda\Console;

/**
 * Encapsulates a resource able to be displayed on console/terminal
 */
interface Stringable
{
    /**
     * Gets string representation of resource to be displayed
     *
     * @return string
     */
    public function toString(): string;
}
