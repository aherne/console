<?php
namespace Test\Lucinda\Console\Styling;

use Lucinda\Console\Styling\Style;
use Lucinda\Console\Styling\Theme;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class ThemeTest
{

    public function define()
    {
        $theme = (new Theme())->define("notice", ["color" => "red"]);
        return (new Strings((string) $theme->get("notice")->get("color")))->assertEquals("red");
    }
        

    public function get()
    {
        return (new Objects((new Theme())->get("h1")))->assertInstanceOf(Style::class);
    }
        

    public function withLightColors()
    {
        return (new Strings((string) (new Theme())->withLightColors()->get("h1")->get("color")))->assertEquals("blue");
    }
        

    public function withoutColors()
    {
        return (new Booleans((new Theme())->withoutColors()->get("h1")->get("underline") === true))->assertTrue();
    }
        

    public function withHighContrast()
    {
        return (new Strings((string) (new Theme())->withHighContrast()->get("warning")->get("background")))->assertEquals("bright-yellow");
    }
        

}
