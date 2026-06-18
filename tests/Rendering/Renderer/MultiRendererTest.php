<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\MultiRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
    
class MultiRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $lines = (new MultiRenderer($helper->context()))->render([
            $helper->element("p", [], [$helper->text("Hello")]),
            $helper->element("show", ["max-width" => "10"], [$helper->element("p", [], [$helper->text("Visible")])])
        ], 10);
        return [
            (new Arrays($lines))->assertSize(2),
            (new Strings(implode("\n", $lines)))->assertContains("Visible")
        ];
    }
        

}
