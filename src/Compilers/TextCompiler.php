<?php

namespace Lucinda\Console\Compilers;

/**
 * Compiles body of text for <span>/<u>/<i>/<b> tag references
 */
class TextCompiler extends AbstractCompiler
{
    /**
     * {@inheritDoc}
     *
     * @see \Lucinda\Console\Compilers\AbstractCompiler::compile()
     */
    protected function compile(string $html): string
    {
        $allowedsubtags = ["span", "i", "u", "b"];
        foreach ($allowedsubtags as $tag) {
            $style = match ($tag) {
                "i" => "font-style:ITALIC",
                "u" => "font-style:UNDERLINE",
                "b" => "font-style:BOLD",
                default => ""
            };

            $pattern = "/<".$tag."(\s+style\s*=\s*\"([^\"]+)\")?>(.+?)<\/".$tag.">/";
            $html = preg_replace_callback(
                $pattern,
                function ($matches) use ($style) {
                    $text = $this->getText($matches[3], $matches[2].($style ? ";".$style : ""));
                    return ($this->isWindows ? $text->getOriginalValue() : $text->getStyledValue());
                },
                $html
            );
        }
        return $html;
    }
}
