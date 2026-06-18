<?php
namespace Test\Lucinda\Console\Rendering;

use Lucinda\Console\Language\Parser;
use Lucinda\Console\Rendering\TerminalRenderer;
use Lucinda\Console\Styling\Theme;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Strings;
    
class TerminalRendererTest
{

    public function render()
    {
        $helper = new Helper();
        $document = (new Parser())->parse("<view><h1>Title</h1><p>Hello</p></view>");
        $output = (new TerminalRenderer(false))->render($document, $helper->environment(), new Theme());
        return (new Strings($output))->assertEquals("Title\n\nHello");
    }
        

}
