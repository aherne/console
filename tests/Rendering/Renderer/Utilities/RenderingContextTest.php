<?php
namespace Test\Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\Renderer\Utilities\RenderingContext;
use Lucinda\Console\Rendering\AnsiPainter;
use Lucinda\Console\Rendering\DisplayWidth;
use Lucinda\Console\Styling\StyleResolver;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
    
class RenderingContextTest
{
    private function context(): RenderingContext
    {
        return (new Helper())->context(80, \Lucinda\Console\Terminal\ColorDepth::ANSI16, true, true);
    }

    public function getStyles()
    {
        return (new Objects($this->context()->getStyles()))->assertInstanceOf(StyleResolver::class);
    }
        

    public function getDisplayWidth()
    {
        return (new Objects($this->context()->getDisplayWidth()))->assertInstanceOf(DisplayWidth::class);
    }
        

    public function getEnvironment()
    {
        return (new Objects($this->context()->getEnvironment()))->assertInstanceOf(Results::class);
    }
        

    public function getAnsiPainter()
    {
        return (new Objects($this->context()->getAnsiPainter()))->assertInstanceOf(AnsiPainter::class);
    }
        

    public function getAnsi()
    {
        return (new Booleans($this->context()->getAnsi()))->assertTrue();
    }
        

}
