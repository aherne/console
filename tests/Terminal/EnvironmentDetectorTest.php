<?php
namespace Test\Lucinda\Console\Terminal;

use Lucinda\Console\Terminal\EnvironmentDetector;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;
use Lucinda\UnitTest\Validator\Objects;
    
class EnvironmentDetectorTest
{

    public function getResults()
    {
        return (new Objects((new EnvironmentDetector())->getResults()))->assertInstanceOf(Results::class);
    }
        

}
