<?php
namespace Test\Lucinda\Console;

use Lucinda\Console\Engine;
use Lucinda\Console\Language\DocumentNode;
use Lucinda\Console\RenderMode;
use Lucinda\Console\Terminal\ColorDepth;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class EngineTest
{

    public function compile()
    {
        $document = (new Engine())->compile("<p>Hello</p>");
        return (new Objects($document))->assertInstanceOf(DocumentNode::class);
    }
        

    public function render()
    {
        $helper = new Helper();
        $engine = new Engine();
        $plain = $engine->render("<p>Hello <strong>world</strong></p>", $helper->environment(), RenderMode::PLAIN_TEXT);
        $ansi = $engine->render("<error>Failure</error>", $helper->environment(80, ColorDepth::ANSI16), RenderMode::ANSI);
        return [
            (new Strings($plain))->assertEquals("Hello world", "plain render"),
            (new Strings($ansi))->assertContains("\e[", "ansi render")
        ];
    }
        

}
