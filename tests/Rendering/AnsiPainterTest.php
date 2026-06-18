<?php
namespace Test\Lucinda\Console\Rendering;

use Lucinda\Console\Rendering\AnsiPainter;
use Lucinda\Console\Rendering\Color;
use Lucinda\Console\Styling\Style;
use Lucinda\Console\Terminal\ColorDepth;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class AnsiPainterTest
{

    public function paint()
    {
        $helper = new Helper();
        $painter = new AnsiPainter(new Color());
        $styled = $painter->paint(
            "Error",
            new Style(["bold" => true, "underline" => true, "color" => "red"]),
            $helper->environment(80, ColorDepth::ANSI16)
        );
        $linked = $painter->paint(
            "Docs",
            new Style(),
            $helper->environment(80, ColorDepth::ANSI16, true, true),
            "https://example.com"
        );
        return [
            (new Strings($styled))->assertContains("\e[1;4;31mError\e[0m", "style codes"),
            (new Strings($linked))->assertContains("\e]8;;https://example.com", "hyperlink")
        ];
    }
        

}
