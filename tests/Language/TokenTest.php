<?php
namespace Test\Lucinda\Console\Language;

use Lucinda\Console\Language\Token;
use Lucinda\Console\Language\SourcePosition;
use Lucinda\Console\Language\TokenType;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class TokenTest
{
    private function token(): Token
    {
        return new Token(TokenType::OPEN_TAG, "p", ["class" => "lead"], (new Helper())->position());
    }

    public function getType()
    {
        return (new Objects($this->token()->getType()))->assertInstanceOf(TokenType::class);
    }
        

    public function getValue()
    {
        return (new Strings($this->token()->getValue()))->assertEquals("p");
    }
        

    public function getAttributes()
    {
        return (new Arrays($this->token()->getAttributes()))->assertEquals(["class" => "lead"]);
    }
        

    public function getPosition()
    {
        return (new Objects($this->token()->getPosition()))->assertInstanceOf(SourcePosition::class);
    }
        

}
