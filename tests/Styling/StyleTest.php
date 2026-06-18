<?php
namespace Test\Lucinda\Console\Styling;

use Lucinda\Console\Styling\Style;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
    
class StyleTest
{

    public function merge()
    {
        $style = (new Style(["color" => "red", "bold" => false]))->merge(new Style(["bold" => true]));
        return (new Booleans($style->get("bold") === true))->assertTrue();
    }
        

    public function get()
    {
        return (new Strings((string) (new Style(["color" => "red"]))->get("color")))->assertEquals("red");
    }
        

    public function all()
    {
        return (new Arrays((new Style(["bold" => true]))->all()))->assertEquals(["bold" => true]);
    }
        

}
