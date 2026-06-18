<?php
namespace Test\Lucinda\Console\Interactive;

use Lucinda\Console\Exception;
use Lucinda\Console\Interactive\LiveSpinner;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Booleans;
    
class LiveSpinnerTest
{

    public function tick()
    {
        $helper = new Helper();
        $spinner = new LiveSpinner($helper->environment(80, \Lucinda\Console\Terminal\ColorDepth::NONE, false, false, true), "Loading");
        $spinner->tick();
        return [
            (new Booleans(true))->assertTrue("interactive tick executed"),
            (new Booleans($helper->throws(fn () => new LiveSpinner($helper->environment()), Exception::class)))->assertTrue("non-interactive rejected")
        ];
    }
        

    public function finish()
    {
        $spinner = new LiveSpinner((new Helper())->environment(80, \Lucinda\Console\Terminal\ColorDepth::NONE, false, false, true));
        $spinner->finish("Done");
        return (new Booleans(true))->assertTrue("interactive finish executed");
    }
        

}
