<?php
namespace Test\Lucinda\Console\Rendering;

use Lucinda\Console\Exception;
use Lucinda\Console\Rendering\Color;
use Lucinda\Console\Terminal\ColorDepth;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
    
class ColorTest
{

    public function getAnsiCode()
    {
        $color = new Color();
        return [
            (new Strings((string) $color->getAnsiCode("red", ColorDepth::ANSI16)))->assertEquals("31", "ansi16"),
            (new Strings((string) $color->getAnsiCode("#ff0000", ColorDepth::TRUE_COLOR)))->assertEquals("38;2;255;0;0", "true color"),
            (new Strings((string) $color->getAnsiCode("ansi-208", ColorDepth::ANSI256)))->assertEquals("38;5;208", "ansi256"),
            (new Strings((string) $color->getAnsiCode("default", ColorDepth::ANSI16, true)))->assertEquals("49", "default background"),
            (new Booleans($color->getAnsiCode("red", ColorDepth::NONE) === null))->assertTrue("no color")
        ];
    }
        

    public function validate()
    {
        $color = new Color();
        $helper = new Helper();
        return [
            (new Booleans(!$helper->throws(fn () => $color->validate("rgb(1, 2, 3)"), Exception::class)))->assertTrue("valid rgb"),
            (new Booleans($helper->throws(fn () => $color->validate("rgb(999, 2, 3)"), Exception::class)))->assertTrue("invalid rgb")
        ];
    }
        

}
