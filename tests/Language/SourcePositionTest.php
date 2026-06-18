<?php
namespace Test\Lucinda\Console\Language;

use Lucinda\Console\Language\SourcePosition;
use Lucinda\UnitTest\Validator\Integers;
    
class SourcePositionTest
{

    public function getLine()
    {
        return (new Integers((new SourcePosition(7, 3))->getLine()))->assertEquals(7);
    }
        

    public function getColumn()
    {
        return (new Integers((new SourcePosition(7, 3))->getColumn()))->assertEquals(3);
    }
        

}
