<?php

namespace Lucinda\Console\Language;

use Lucinda\Console\Exception;

final class ParseException extends Exception
{
    public function __construct(string $message, SourcePosition $position)
    {
        parent::__construct($message." at line ".$position->getLine().", column ".$position->getColumn());
    }
}
