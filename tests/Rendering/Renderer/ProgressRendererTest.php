<?php
namespace Test\Lucinda\Console\Rendering\Renderer;

use Lucinda\Console\Rendering\Renderer\ProgressRenderer;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class ProgressRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $progress = $helper->element("progress", ["value" => "5", "max" => "10", "label" => "50%"]);
        return (new Strings((new ProgressRenderer($helper->context(20, \Lucinda\Console\Terminal\ColorDepth::NONE, false)))->render($progress, 20)))->assertContains("50%");
    }
        

}
