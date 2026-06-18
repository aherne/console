<?php
namespace Test\Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\Renderer\Utilities\InlineLayout;
use Lucinda\Console\Rendering\InlineSegment;
use Lucinda\Console\Styling\Style;
use Lucinda\Console\Terminal\ColorDepth;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
    
class InlineLayoutTest
{

    public function renderSingleLine()
    {
        $layout = new InlineLayout((new Helper())->context());
        return (new Strings($layout->renderSingleLine([new InlineSegment("abcdef", new Style())], 4, "ellipsis")))->assertEquals("abc…");
    }
        

    public function renderCharacterWrapped()
    {
        $layout = new InlineLayout((new Helper())->context());
        return (new Arrays($layout->renderCharacterWrapped([new InlineSegment("abcd", new Style())], 2)))->assertEquals(["ab", "cd"]);
    }
        

    public function paintSegments()
    {
        $layout = new InlineLayout((new Helper())->context(80, ColorDepth::ANSI16, true, true));
        $output = $layout->paintSegments([new InlineSegment("x", new Style(["bold" => true]))]);
        return (new Strings($output))->assertContains("\e[1m");
    }
        

    public function collectSegments()
    {
        $helper = new Helper();
        $layout = new InlineLayout($helper->context());
        $segments = [];
        $layout->collectSegments([$helper->element("link", ["href" => "https://example.com"], [$helper->text("Docs")])], new Style(), $segments, null, false);
        return [
            (new Arrays($segments))->assertSize(2),
            (new Strings($segments[1]->getText()))->assertEquals(" (https://example.com)")
        ];
    }
        

    public function appendSegment()
    {
        $layout = new InlineLayout((new Helper())->context());
        $line = [new InlineSegment("a", new Style())];
        $layout->appendSegment($line, new InlineSegment("b", new Style()));
        return (new Strings($line[0]->getText()))->assertEquals("ab");
    }
        

    public function splitToWidth()
    {
        $chunks = (new InlineLayout((new Helper())->context()))->splitToWidth("abcd", 2);
        return (new Arrays($chunks))->assertEquals(["ab", "cd"]);
    }
        

}
