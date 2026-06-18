<?php
namespace Test\Lucinda\Console\Rendering;

use Lucinda\Console\Rendering\InlineSegment;
use Lucinda\Console\Styling\Style;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class InlineSegmentTest
{
    private function segment(): InlineSegment
    {
        return new InlineSegment("text", new Style(["bold" => true]), "https://example.com");
    }

    public function getText()
    {
        return (new Strings($this->segment()->getText()))->assertEquals("text");
    }
        

    public function getStyle()
    {
        return (new Objects($this->segment()->getStyle()))->assertInstanceOf(Style::class);
    }
        

    public function getHref()
    {
        return (new Strings((string) $this->segment()->getHref()))->assertEquals("https://example.com");
    }
        

}
