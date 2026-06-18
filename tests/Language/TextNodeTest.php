<?php
namespace Test\Lucinda\Console\Language;

use Test\Lucinda\Console\Helper;
use Lucinda\Console\Language\SourcePosition;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class TextNodeTest
{

    public function getPosition()
    {
        $node = (new Helper())->text("hello");
        return (new Objects($node->getPosition()))->assertInstanceOf(SourcePosition::class);
    }
        

    public function getValue()
    {
        return (new Strings((new Helper())->text("hello")->getValue()))->assertEquals("hello");
    }
        

}
