<?php
namespace Test\Lucinda\Console\Language;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\ParseException;
use Lucinda\Console\Language\Parser;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class ParserTest
{

    public function parse()
    {
        $document = (new Parser())->parse('<section><p bold>Hello <span>world</span></p><br/></section>');
        $section = $document->getChildren()[0];
        $paragraph = $section->getChildren()[0];
        return [
            (new Arrays($document->getChildren()))->assertSize(1, "document children"),
            (new Strings($section->getName()))->assertEquals("section", "root element"),
            (new Booleans($paragraph->getAttribute("bold") === true))->assertTrue("boolean attribute"),
            (new Objects($paragraph->getChildren()[1]))->assertInstanceOf(ElementNode::class, "nested element"),
            (new Booleans((new Helper())->throws(fn () => (new Parser())->parse("<p><strong>x</p>"), ParseException::class)))->assertTrue("malformed nesting")
        ];
    }
        

}
