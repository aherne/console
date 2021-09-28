<?php
namespace Lucinda\Console\Compilers;

/**
 * Compiles body of text for <div> tag references
 */
class DivCompiler extends AbstractCompiler
{
    /**
     * {@inheritDoc}
     * @see \Lucinda\Console\Compilers\AbstractCompiler::compile()
     */
    protected function compile(string $html): string
    {
        return preg_replace_callback("/<div(\s+style\s*=\s*\"([^\"]+)\")?>(.+?)<\/div>/", function($matches) {
            $style = $matches[2];
            $body = $matches[3];
            $subCompiler = new TextCompiler($body, $this->isWindows);
            $text = $this->getText($subCompiler->getBody(), $style);
            return ($this->isWindows?$text->getOriginalValue():$text->getStyledValue());
        }, $html);
    }
}

