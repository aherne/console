<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\TableRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class TableRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $table = $helper->element("table", ["border" => "ascii"], [
            $helper->element("tbody", [], [
                $helper->element("tr", [], [
                    $helper->element("td", [], [$helper->text("A")]),
                    $helper->element("td", ["align" => "right"], [$helper->text("2")])
                ])
            ])
        ]);
        $output = implode("\n", (new TableRenderer($helper->context(20, \Lucinda\Console\Terminal\ColorDepth::NONE, false)))->render($table, 20));
        return [
            (new Strings($output))->assertContains("+"),
            (new Strings($output))->assertContains(" A ")
        ];
    }
        

}
