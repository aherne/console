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
        $pattern = '/<(span|i|u|b)(\s+style\s*=\s*"([^"]+)")?>'
            .'((?:(?!<(?:span|i|u|b)\b).)*?)<\/\1>/is';

        do {
            $previous = $html;
            $html = preg_replace_callback(
                $pattern,
                function ($matches) {
                    $style = match (strtolower($matches[1])) {
                        "i" => "font-style:ITALIC",
                        "u" => "font-style:UNDERLINE",
                        "b" => "font-style:BOLD",
                        default => ""
                    };
                    $style = $matches[3].($style ? ";".$style : "");
                    $text = $this->getText($matches[4], $style);

                    return $this->isWindows ? $text->getOriginalValue() : $text->getStyledValue();
                },
                $html
            );
        } while ($html !== $previous);

        return $html;
    }
}
