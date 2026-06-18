<?php
namespace Test\Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\Renderer\Utilities\TextLayout;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class TextLayoutTest
{

    public function align()
    {
        $layout = new TextLayout((new Helper())->context());
        return [
            (new Strings($layout->align("x", 3, "right")))->assertEquals("  x", "right"),
            (new Strings($layout->align("x", 3, "center")))->assertEquals(" x ", "center")
        ];
    }
        

    public function pad()
    {
        return (new Strings((new TextLayout((new Helper())->context()))->pad("x", 3)))->assertEquals("x  ");
    }
        

}
