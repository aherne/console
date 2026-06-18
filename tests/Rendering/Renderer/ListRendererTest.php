<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\ListRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class ListRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $list = $helper->element("ol", ["marker" => "roman", "start" => "4"], [
            $helper->element("li", [], [$helper->text("First")]),
            $helper->element("li", [], [$helper->text("Second")])
        ]);
        $output = implode("\n", (new ListRenderer($helper->context()))->render($list, 80));
        return [
            (new Strings($output))->assertContains("iv. First"),
            (new Strings($output))->assertContains("v. Second")
        ];
    }
        

}
