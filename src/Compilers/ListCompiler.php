<?php

namespace Lucinda\Console\Compilers;

use Lucinda\Console\AbstractList;
use Lucinda\Console\UnorderedList;
use Lucinda\Console\OrderedList;
use Lucinda\Console\Exception;

/**
 * Compiles body of text for <ul>/<ol> tag references
 */
class ListCompiler extends AbstractCompiler
{
    /**
     * @var array<int,AbstractList>
     */
    private array $entries = [];

    /**
     * {@inheritDoc}
     *
     * @see \Lucinda\Console\Compilers\AbstractCompiler::compile()
     */
    protected function compile(string $html): string
    {
        $html = preg_replace_callback(
            "/<ul>(?!.*<ul>)(.+?)<\/ul>/mis",
            function ($matches) {
                $list = new UnorderedList();
                return $this->parseList($list, $matches[1]);
            },
            $html
        );

        $html = preg_replace_callback(
            "/<ol>(?!.*<ol>)(.+?)<\/ol>/mis",
            function ($matches) {
                $list = new OrderedList();
                return $this->parseList($list, $matches[1]);
            },
            $html
        );

        if (str_contains($html, "</li>")) {
            return $this->compile($html);
        } else {
            return preg_replace_callback(
                "/~list([0-9]+)~/",
                function ($matches) {
                    return $this->entries[(int) $matches[1]]->__toString();
                },
                $html
            );
        }
    }

    /**
     * Recursively parses body of <ul>/<ol> tag, returning references to be replaced later
     *
     * @param  AbstractList $list
     * @param  string       $body
     * @throws Exception
     * @return string
     */
    private function parseList(AbstractList $list, string $body): string
    {
        // set caption
        $m1 = [];
        preg_match("/<caption(\s+style\s*=\s*\"([^\"]+)\")?>(.+?)<\/caption>/", $body, $m1);
        if (!empty($m1[3])) {
            $style = $m1[2];
            $subbody = $m1[3];
            $subCompiler = new TextCompiler($subbody, $this->isWindows);
            $tmp = $this->getText($subCompiler->getBody(), $style);
            $list->setCaption($this->isWindows ? $tmp->getOriginalValue() : $tmp);
        }

        // set entries
        $m2 = [];
        preg_match_all("/<li(\s+style\s*=\s*\"([^\"]+)\")?>(.+?)<\/li>/mis", $body, $m2);
        if (empty($m2[3])) {
            throw new Exception("List must have a minimum of one entry!");
        }
        foreach ($m2[3] as $i=>$text) {
            $text = trim($text);
            $m3 = [];
            preg_match("/^~list([0-9]+)~$/", $text, $m3);
            if (!empty($m3)) {
                $list->addList($this->entries[$m3[1]]);
            } else {
                $subbody = $text;
                $subCompiler = new TextCompiler($subbody, $this->isWindows);
                $tmp = $this->getText($subCompiler->getBody(), $m2[2][$i]);
                $list->addItem($this->isWindows ? $tmp->getOriginalValue() : $tmp);
            }
        }

        // add to entries
        $id = sizeof($this->entries);
        $this->entries[$id] = $list;

        return "~list".$id."~";
    }
}
