<?php
namespace Lucinda\Console;

/**
 * Parses a pseudo-HTML and builds a console text ready for display
 */
class Wrapper
{
    private $body;
    private $isWindows;

    /**
     * Parses pseudo-HTML received, taking into account if platform has styling abilities
     *
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
        $this->isWindows = stripos(php_uname("s"), "win")!==false;

        $this->setBody();
    }

    /**
     * Sets body of text to be displayed
     *
     * @throws Exception If an error prevents proceding any further
     */
    private function setBody(): void
    {
        $output = "";
        $xml = simplexml_load_string("<xml>".$this->body."</xml>");
        foreach ($xml->children() as $child) {
            $tag = $child->getName();
            switch ($tag) {
                case "div":
                    $text = $this->parseText($child);
                    $output .= ($text instanceof Text ? $text->toString() : $text)."\n";
                    break;
                case "table":
                    $table = $this->parseTable($child);
                    $output .= $table->toString()."\n";
                    break;
                case "ul":
                    $list = $this->parseList($child, null, "Lucinda\\Console\\UnorderedList", "ul");
                    $output .= $list->toString()."\n";
                    break;
                case "ol":
                    $list = $this->parseList($child, null, "Lucinda\\Console\\OrderedList", "ol");
                    $output .= $list->toString()."\n";
                    break;
                case "br":
                    $output .= "\n";
                    break;
                default:
                    throw new Exception("Invalid tag: ".$tag);
                    break;
            }
        }
        $this->body = $output;
    }

    /**
     * Parses XML tag for text (styled, if any)
     *
     * @param \SimpleXMLElement $child
     * @throws Exception
     * @return \Lucinda\Console\Text|string
     */
    private function parseText(\SimpleXMLElement $child)
    {
        $name = (string) $child;
        $style = (string) $child["style"];
        if ($style && !$this->isWindows) {
            $text = new Text($name);
            $parts = explode(":", $style);
            if (sizeof($parts)!=2) {
                throw new Exception("Invalid style: ".$style);
            }
            $name = trim($parts[0]);
            $value = strtoupper(trim($parts[1]));
            switch ($name) {
                case "font-weight":
                    if (defined("\Lucinda\Console\FontStyle::".$value)) {
                        $text->setFontStyle(constant("\Lucinda\Console\FontStyle::".$value));
                    }
                    break;
                case "background-color":
                    if (defined("\Lucinda\Console\BackgroundColor::".$value)) {
                        $text->setFontStyle(constant("\Lucinda\Console\BackgroundColor::".$value));
                    }
                    break;
                case "color":
                    if (defined("\Lucinda\Console\ForegroundColor::".$value)) {
                        $text->setFontStyle(constant("\Lucinda\Console\ForegroundColor::".$value));
                    }
                    break;
                default:
                    throw new Exception("Invalid style: ".$style);
                    break;
            }
            return $text;
        } else {
            return $name;
        }
    }

    /**
     * Parses XML tag for (recursive) lists (ordered or unordered)
     *
     * @param \SimpleXMLElement $child
     * @param AbstractList $parent
     * @param string $className
     * @param string $tagName
     * @return AbstractList
     */
    private function parseList(\SimpleXMLElement $child, AbstractList $parent = null, string $className, string $tagName): AbstractList
    {
        $caption = null;
        $captionXML = $child->caption;
        if ($captionXML) {
            $caption = $this->parseText($captionXML);
        }
        if ($parent) {
            $list = $parent->addList($caption);
        } else {
            $list = new $className($caption);
        }
        $elements = $child->li;
        foreach ($elements as $element) {
            if ($element->$tagName) {
                $this->parseList($element->$tagName, $list, $className, $tagName);
            } else {
                $list->addItem($this->parseText($element));
            }
        }
        return $list;
    }

    /**
     * Parses XML tag for tables
     *
     * @param \SimpleXMLElement $child
     * @throws Exception
     * @return Table
     */
    private function parseTable(\SimpleXMLElement $child): Table
    {
        $top = $child->thead->tr;
        if (!$top) {
            throw new Exception("Table missing thead");
        }
        $columns = [];
        foreach ($top->children() as $c1) {
            $columns[] = $this->parseText($c1);
        }
        $table = new Table($columns);
        $bottom = $child->tbody;
        if (!$bottom) {
            throw new Exception("Table missing tbody");
        }
        foreach ($bottom->children() as $c2) {
            $row = [];
            foreach ($c2->children() as $c3) {
                $row[] = $this->parseText($c3);
            }
            $table->addRow($row);
        }
        return $table;
    }

    /**
     * Gets payload to be displayed back to users
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
