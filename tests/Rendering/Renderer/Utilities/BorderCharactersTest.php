<?php
namespace Test\Lucinda\Console\Rendering\Renderer\Utilities;

use Lucinda\Console\Rendering\Renderer\Utilities\BorderCharacters;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Strings;
    
class BorderCharactersTest
{

    public function borderCharacters()
    {
        $unicode = new BorderCharacters((new Helper())->context());
        $ascii = new BorderCharacters((new Helper())->context(80, \Lucinda\Console\Terminal\ColorDepth::NONE, false));
        return [
            (new Strings($unicode->borderCharacters("rounded")[0]))->assertEquals("╭", "unicode"),
            (new Strings($ascii->borderCharacters("rounded")[0]))->assertEquals("+", "ascii")
        ];
    }
        

    public function tableBorderCharacters()
    {
        $characters = (new BorderCharacters((new Helper())->context()))->tableBorderCharacters("single");
        return (new Arrays($characters))->assertSize(11);
    }
        

}
