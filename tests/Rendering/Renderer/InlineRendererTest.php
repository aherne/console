<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\InlineRenderer;
use Lucinda\Console\Styling\Style;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
    
class InlineRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $lines = (new InlineRenderer($helper->context()))->render([$helper->text("one two three")], 8, new Style());
        return (new Arrays($lines))->assertEquals(["one two", "three"]);
    }
        

}
