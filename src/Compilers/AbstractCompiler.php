<?php

namespace Lucinda\Console\Compilers;

use Lucinda\Console\BackgroundColor;
use Lucinda\Console\FontStyle;
use Lucinda\Console\ForegroundColor;
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
     * @param string $body      Body to be parsed
     * @param bool   $isWindows Whether or not OS is windows
     */
    public function __construct(string $body, bool $isWindows)
    {
        $this->isWindows = $isWindows;
        $this->body = $this->compile($body);
    }

    /**
     * Parses text received for respective tag and returns compiled result
     *
     * @param  string $body
     * @return string
     */
    abstract protected function compile(string $body): string;

    /**
     * Converts textual tag body into a style-able Text object
     *
     * @param  string $body  Text body
     * @param  string $style Style to apply, if any
     * @throws Exception
     * @return Text
     */
    protected function getText(string $body, string $style): Text
    {
        $text = new Text($body);
        foreach (explode(";", $style) as $declaration) {
            $declaration = trim($declaration);
            if ($declaration === "") {
                continue;
            }

            if (!preg_match("/^([a-zA-Z-]+)\s*:\s*([a-zA-Z_]+)$/", $declaration, $matches)) {
                throw new Exception("Invalid style declaration: ".$declaration);
            }

            $this->applyStyle($text, strtolower($matches[1]), strtoupper($matches[2]));
        }
        return $text;
    }

    /**
     * Applies styles to console text
     *
     * @param  Text   $text
     * @param  string $name
     * @param  string $value
     * @return void
     * @throws Exception
     */
    private function applyStyle(Text $text, string $name, string $value): void
    {
        $enum = match ($name) {
            "font-style" => FontStyle::class,
            "background-color" => BackgroundColor::class,
            "color" => ForegroundColor::class,
            default => throw new Exception("Invalid style: ".$name)
        };

        foreach ($enum::cases() as $case) {
            if ($case->name === $value) {
                match ($name) {
                    "font-style" => $text->setFontStyle($case),
                    "background-color" => $text->setBackgroundColor($case),
                    "color" => $text->setForegroundColor($case)
                };
                return;
            }
        }

        throw new Exception("Invalid value for ".$name.": ".$value);
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
