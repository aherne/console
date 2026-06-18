<?php
namespace Test\Lucinda\Console;

use Lucinda\Console\Exception;
use Lucinda\UnitTest\Validator\Strings;
    
class ExceptionTest
{
    public function message()
    {
        return (new Strings((new Exception("Failure"))->getMessage()))->assertEquals("Failure");
    }

}
