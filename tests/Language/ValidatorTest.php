<?php
namespace Test\Lucinda\Console\Language;

use Lucinda\Console\Language\ParseException;
use Lucinda\Console\Language\Parser;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Booleans;
    
class ValidatorTest
{

    public function validate()
    {
        $parser = new Parser();
        $helper = new Helper();
        return [
            (new Booleans(!$helper->throws(fn () => $parser->parse('<table><tbody><tr><td>A</td></tr></tbody></table>'), ParseException::class)))->assertTrue("valid table"),
            (new Booleans($helper->throws(fn () => $parser->parse("<unknown/>"), ParseException::class)))->assertTrue("unknown tag"),
            (new Booleans($helper->throws(fn () => $parser->parse('<p onclick="x">x</p>'), ParseException::class)))->assertTrue("invalid attribute"),
            (new Booleans($helper->throws(fn () => $parser->parse('<p color="bad">x</p>'), ParseException::class)))->assertTrue("invalid color"),
            (new Booleans($helper->throws(fn () => $parser->parse("<td>x</td>"), ParseException::class)))->assertTrue("invalid structure"),
            (new Booleans($helper->throws(fn () => $parser->parse('<progress value="5" max="0"/>'), ParseException::class)))->assertTrue("invalid progress")
        ];
    }
        

}
