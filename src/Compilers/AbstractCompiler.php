<?php
namespace Lucinda\Console\Compilers;

use Lucinda\Console\FontStyle;
use Lucinda\Console\Text;
use Lucinda\Console\Exception;

/**
 * Blueprint for a compiler that replaces mentions of a given Console-HTML tag with final text
 */
abstract class AbstractCompiler
{
    protected bool $isWindows;
    protected string $body;
    
    /**
     * Starts compilation by body to be parsed
     * 
     * @param string $body Body to be parsed
     * @param bool $isWindows Whether or not OS is windows
     */
    public function __construct(string $body, bool $isWindows)
    {
        $this->isWindows = $isWindows;
        $this->body = $this->compile($body);
    }
    
    /**
     * Parses text received for respective tag and returns compiled result
     * 
     * @param string $body
     * @return string
     */
    abstract protected function compile(string $body): string;
    
    /**
     * Converts textual tag body into a style-able Text object
     * 
     * @param string $body Text body
     * @param string $style Style to apply, if any
     * @throws Exception
     * @return Text
     */
    protected function getText(string $body, string $style): Text
    {
        $text = new Text($body);
        $matches = [];
        preg_match_all("/([a-zA-Z\-]+)\s*\:\s*([a-zA-Z]+)/", $style, $matches);
        foreach ($matches[0] as $k=>$v) {
            $name = strtolower($matches[1][$k]);
            $value = strtoupper($matches[2][$k]);
            switch ($name) {
                case "font-style":
                    $cases = \Lucinda\Console\FontStyle::cases();
                    foreach ($cases as $case) {
                        if ($case->name == $value) {
                            $text->setFontStyle($case);
                        }
                    }
                    break;
                case "background-color":
                    $cases = \Lucinda\Console\BackgroundColor::cases();
                    foreach ($cases as $case) {
                        if ($case->name == $value) {
                            $text->setBackgroundColor($case);
                        }
                    }
                    break;
                case "color":
                    $cases = \Lucinda\Console\ForegroundColor::cases();
                    foreach ($cases as $case) {
                        if ($case->name == $value) {
                            $text->setForegroundColor($case);
                        }
                    }
                    break;
                default:
                    throw new Exception("Invalid style: ".$style);
                    break;
            }
        }
        return $text;
    }
    
    /**
     * Gets final compiled text body 
     * 
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
