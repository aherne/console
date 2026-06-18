<?php
namespace Test\Lucinda\Console\Language;

use Test\Lucinda\Console\Helper;
use Lucinda\Console\Language\SourcePosition;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Objects;
    
class DocumentNodeTest
{

    public function getPosition()
    {
        $document = (new Helper())->document([]);
        return (new Objects($document->getPosition()))->assertInstanceOf(SourcePosition::class);
    }
        

    public function addChild()
    {
        $helper = new Helper();
        $document = $helper->document([]);
        $document->addChild($helper->text("x"));
        return (new Arrays($document->getChildren()))->assertSize(1);
    }
        

    public function setChildren()
    {
        $helper = new Helper();
        $document = $helper->document([$helper->text("x")]);
        $document->setChildren([$helper->text("y"), $helper->text("z")]);
        return (new Arrays($document->getChildren()))->assertSize(2);
    }
        

    public function getChildren()
    {
        $helper = new Helper();
        $children = [$helper->text("x")];
        $document = $helper->document($children);
        return (new Arrays($document->getChildren()))->assertEquals($children);
    }
        

}
