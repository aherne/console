<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\BoxRenderer;
use Lucinda\Console\Rendering\Renderer\MultiRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class BoxRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $box = $helper->element("box", ["title" => "X", "border" => "ascii"], [$helper->element("p", [], [$helper->text("ok")])]);
        $output = implode("\n", (new BoxRenderer($helper->context(20, \Lucinda\Console\Terminal\ColorDepth::NONE, false)))->render($box, 10, new MultiRenderer($helper->context(20, \Lucinda\Console\Terminal\ColorDepth::NONE, false))));
        return (new Strings($output))->assertContains("+ X ");
    }
        

}
