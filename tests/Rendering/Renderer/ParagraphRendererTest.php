<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\ParagraphRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
    
class ParagraphRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $paragraph = $helper->element("p", ["align" => "right", "width" => "6"], [$helper->text("x")]);
        return (new Arrays((new ParagraphRenderer($helper->context()))->render($paragraph, 6)))->assertEquals(["     x"]);
    }
        

}
