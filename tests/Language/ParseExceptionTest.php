<?php
namespace Test\Lucinda\Console\Language;

use Lucinda\Console\Language\ParseException;
use Lucinda\Console\Language\SourcePosition;
use Lucinda\UnitTest\Validator\Strings;
    
class ParseExceptionTest
{
    public function message()
    {
        $exception = new ParseException("Broken", new SourcePosition(4, 9));
        return (new Strings($exception->getMessage()))->assertContains("line 4, column 9");
    }

}
