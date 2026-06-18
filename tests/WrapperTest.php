<?php
namespace Test\Lucinda\Console;

use Lucinda\Console\Wrapper;
use Lucinda\UnitTest\Validator\Strings;
    
class WrapperTest
{

    public function getBody()
    {
        $wrapper = new Wrapper("<p>Hello</p>");
        return (new Strings($wrapper->getBody()))->assertEquals("Hello");
    }
        

    public function display()
    {
        $wrapper = new Wrapper("<p>Hello</p>");
        ob_start();
        $wrapper->display();
        $output = (string) ob_get_clean();
        return (new Strings($output))->assertEquals("Hello");
    }
        

}
