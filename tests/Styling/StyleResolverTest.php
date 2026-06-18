<?php
namespace Test\Lucinda\Console\Styling;

use Lucinda\Console\Styling\StyleResolver;
use Lucinda\Console\Styling\Theme;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
    
class StyleResolverTest
{

    public function resolve()
    {
        $helper = new Helper();
        $theme = (new Theme())->define("notice", ["color" => "red"]);
        $style = (new StyleResolver($theme))->resolve(
            $helper->element("p", ["class" => "notice", "bold" => true, "style" => "align: right"])
        );
        return [
            (new Strings((string) $style->get("color")))->assertEquals("red"),
            (new Booleans($style->get("bold") === true))->assertTrue(),
            (new Strings((string) $style->get("align")))->assertEquals("right")
        ];
    }
        

}
