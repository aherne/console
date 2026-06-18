<?php
namespace Test\Lucinda\Console\Language;

use Test\Lucinda\Console\Helper;
use Lucinda\Console\Language\SourcePosition;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class ElementNodeTest
{

    public function getPosition()
    {
        return (new Objects((new Helper())->element("p")->getPosition()))->assertInstanceOf(SourcePosition::class);
    }
        

    public function getAttribute()
    {
        $node = (new Helper())->element("p", ["bold" => true, "class" => "lead"]);
        return [
            (new Booleans($node->getAttribute("bold")))->assertTrue("boolean attribute"),
            (new Strings((string) $node->getAttribute("class")))->assertEquals("lead", "string attribute")
        ];
    }
        

    public function getName()
    {
        return (new Strings((new Helper())->element("section")->getName()))->assertEquals("section");
    }
        

    public function getAttributes()
    {
        $attributes = ["class" => "lead"];
        return (new Arrays((new Helper())->element("p", $attributes)->getAttributes()))->assertEquals($attributes);
    }
        

    public function addChild()
    {
        $helper = new Helper();
        $node = $helper->element("p");
        $node->addChild($helper->text("x"));
        return (new Arrays($node->getChildren()))->assertSize(1);
    }
        

    public function setChildren()
    {
        $helper = new Helper();
        $node = $helper->element("p");
        $node->setChildren([$helper->text("x"), $helper->text("y")]);
        return (new Arrays($node->getChildren()))->assertSize(2);
    }
        

    public function getChildren()
    {
        $helper = new Helper();
        $children = [$helper->text("x")];
        return (new Arrays($helper->element("p", [], $children)->getChildren()))->assertEquals($children);
    }
        

}
