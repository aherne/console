<?php
namespace Test\Lucinda\Console\Terminal\EnvironmentDetector;

use Lucinda\Console\Terminal\ColorDepth;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Objects;
    
class ResultsTest
{
    private function results(): Results
    {
        return new Results();
    }

    public function setWidth()
    {
        $results = $this->results();
        $results->setWidth(120);
        return (new Integers($results->getWidth()))->assertEquals(120);
    }
        

    public function getWidth()
    {
        return (new Integers($this->results()->getWidth()))->assertEquals(80);
    }
        

    public function setHeight()
    {
        $results = $this->results();
        $results->setHeight(40);
        return (new Integers($results->getHeight()))->assertEquals(40);
    }
        

    public function getHeight()
    {
        return (new Integers($this->results()->getHeight()))->assertEquals(24);
    }
        

    public function setColorDepth()
    {
        $results = $this->results();
        $results->setColorDepth(ColorDepth::TRUE_COLOR);
        return (new Objects($results->getColorDepth()))->assertInstanceOf(ColorDepth::class);
    }
        

    public function getColorDepth()
    {
        return (new Objects($this->results()->getColorDepth()))->assertInstanceOf(ColorDepth::class);
    }
        

    public function setUnicode()
    {
        $results = $this->results();
        $results->setUnicode(false);
        return (new Booleans($results->getUnicode()))->assertFalse();
    }
        

    public function getUnicode()
    {
        return (new Booleans($this->results()->getUnicode()))->assertTrue();
    }
        

    public function setHyperlinks()
    {
        $results = $this->results();
        $results->setHyperlinks(true);
        return (new Booleans($results->getHyperlinks()))->assertTrue();
    }
        

    public function getHyperlinks()
    {
        return (new Booleans($this->results()->getHyperlinks()))->assertFalse();
    }
        

    public function setInteractive()
    {
        $results = $this->results();
        $results->setInteractive(true);
        return (new Booleans($results->getInteractive()))->assertTrue();
    }
        

    public function getInteractive()
    {
        return (new Booleans($this->results()->getInteractive()))->assertFalse();
    }
        

}
