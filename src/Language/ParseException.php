<?php

namespace Lucinda\Console\Language;

use Lucinda\Console\Exception;

/**
 * Exception raised when markup cannot be tokenized, parsed, or validated.
 */
final class ParseException extends Exception
{
    /**
     * Creates an exception message annotated with line and column information.
     *
     * @param string $message
     * @param SourcePosition $position
     * @return void
     */
    public function __construct(string $message, SourcePosition $position)
    {
        parent::__construct($message." at line ".$position->getLine().", column ".$position->getColumn());
    }
}
