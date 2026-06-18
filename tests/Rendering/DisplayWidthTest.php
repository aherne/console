<?php
namespace Test\Lucinda\Console\Rendering;

use Lucinda\Console\Rendering\DisplayWidth;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;
    
class DisplayWidthTest
{

    public function get()
    {
        return (new Integers((new DisplayWidth())->get("é")))->assertEquals(1);
    }
        

    public function stripControlSequences()
    {
        return (new Strings((new DisplayWidth())->stripControlSequences("\e[31mred\e[0m")))->assertEquals("red");
    }
        

    public function escape()
    {
        return (new Strings((new DisplayWidth())->escape("a\e[31mb")))->assertEquals("ab");
    }
        

}
