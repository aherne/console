<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\ColumnsRenderer;
use Lucinda\Console\Rendering\Renderer\MultiRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class ColumnsRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $columns = $helper->element("columns", ["gap" => "1"], [
            $helper->element("column", ["width" => "4"], [$helper->element("p", [], [$helper->text("A")])]),
            $helper->element("column", [], [$helper->element("p", [], [$helper->text("B")])])
        ]);
        $output = implode("\n", (new ColumnsRenderer($helper->context()))->render($columns, 10, new MultiRenderer($helper->context())));
        return (new Strings($output))->assertContains("A");
    }
        

}
