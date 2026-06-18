<?php
namespace Test\Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\Renderer\Utilities\WidthResolver;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Integers;
    
class WidthResolverTest
{

    public function allocateWidths()
    {
        $helper = new Helper();
        $resolver = new WidthResolver($helper->context());
        $widths = $resolver->allocateWidths([
            $helper->element("column", ["width" => "50%"]),
            $helper->element("column")
        ], 20);
        return [
            (new Arrays($widths))->assertSize(2),
            (new Integers(array_sum($widths)))->assertEquals(20)
        ];
    }
        

    public function resolveWidth()
    {
        $helper = new Helper();
        $width = (new WidthResolver($helper->context()))->resolveWidth(
            $helper->element("p", ["width" => "50%", "min-width" => "5"]),
            40
        );
        return (new Integers($width))->assertEquals(20);
    }
        

}
