<?php
namespace Test\Lucinda\Console\Language;

use Lucinda\Console\Language\ParseException;
use Lucinda\Console\Language\Tokenizer;
use Lucinda\Console\Language\TokenType;
use Test\Lucinda\Console\Helper;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
    
class TokenizerTest
{

    public function tokenize()
    {
        $tokens = (new Tokenizer('<p bold class="lead">Hello &amp; bye<br/></p><!--x-->'))->tokenize();
        return [
            (new Arrays($tokens))->assertSize(4, "token count"),
            (new Objects($tokens[0]->getType()))->assertInstanceOf(TokenType::class, "token type"),
            (new Booleans($tokens[0]->getAttributes()["bold"] ?? false))->assertTrue("boolean attribute"),
            (new Strings($tokens[1]->getValue()))->assertEquals("Hello & bye", "entity decoding"),
            (new Booleans((new Helper())->throws(fn () => (new Tokenizer("<p"))->tokenize(), ParseException::class)))->assertTrue("malformed tag")
        ];
    }
        

}
