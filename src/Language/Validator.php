<?php

namespace Lucinda\Console\Language;

use Lucinda\Console\Exception;
use Lucinda\Console\Rendering\Color;

final class Validator
{
    private const STYLE_PROPERTIES = [
        "color", "background", "bold", "dim", "italic", "underline", "blink", "inverse", "hidden",
        "strikethrough", "width", "min-width", "max-width", "align", "vertical-align", "wrap", "overflow",
        "indent", "padding", "margin", "margin-top", "margin-bottom"
    ];
    private const TAGS = [
        "view", "section", "h1", "h2", "h3", "h4", "h5", "h6", "p", "span", "strong", "b",
        "em", "i", "u", "s", "code", "kbd", "success", "info", "warning", "error", "box", "columns",
        "column", "table", "thead", "tbody", "tr", "th", "td", "ol", "ul", "li", "hr", "br", "spacer",
        "link", "badge", "progress", "spinner", "show"
    ];
    private const GLOBAL_ATTRIBUTES = [
        "class", "style", "color", "background", "bold", "dim", "italic", "underline", "blink", "inverse",
        "hidden", "strikethrough", "width", "min-width", "max-width", "align", "vertical-align", "wrap",
        "overflow", "indent", "padding", "margin", "margin-top", "margin-bottom"
    ];
    private const ANSI_STYLES = [
        "align" => ["left", "center", "right"],
        "vertical-align" => ["top", "middle", "bottom"],
        "wrap" => ["word", "character", "none"],
        "overflow" => ["wrap", "clip", "ellipsis"],
        "border" => ["none", "ascii", "single", "double", "rounded", "heavy"]
    ];
    private const TAG_ATTRIBUTES = [
        "view" => ["theme"],
        "section" => ["name"],
        "box" => ["title", "border"],
        "columns" => ["gap"],
        "column" => ["role"],
        "table" => ["border", "zebra", "empty"],
        "th" => ["colspan"],
        "td" => ["colspan"],
        "ol" => ["marker", "start"],
        "ul" => ["marker"],
        "spacer" => ["lines"],
        "link" => ["href"],
        "badge" => [],
        "progress" => ["value", "max", "label"],
        "spinner" => ["label", "frame"],
        "show" => []
    ];
    private const ATTRIBUTES_1 = ["min-width", "max-width", "indent", "padding", "margin", "margin-top", "margin-bottom"];
    private const ATTRIBUTES_2 = ["indent", "padding", "margin", "margin-top", "margin-bottom", "gap", "lines", "start", "colspan"];

    private Color $color;

    public function __construct(?Color $color = null)
    {
        $this->color = $color ?? new Color();
    }

    public function validate(DocumentNode $document): void
    {
        $this->validateChildren($document->getChildren(), null, 0);
    }

    /**
     * @param Node[] $children
     */
    private function validateChildren(array $children, ?ElementNode $parent, int $depth): void
    {
        if ($depth > 100) {
            throw new ParseException("Maximum document depth exceeded", $parent?->getPosition() ?? new SourcePosition(1, 1));
        }

        foreach ($children as $child) {
            if (!$child instanceof ElementNode) {
                continue;
            }
            if (!in_array($child->getName(), self::TAGS, true)) {
                throw new ParseException("Unknown tag: ".$child->getName(), $child->getPosition());
            }
            $this->validateAttributes($child);
            $this->validateStructure($child, $parent);
            $this->validateChildren($child->getChildren(), $child, $depth+1);
        }
    }

    // break me
    private function validateAttributes(ElementNode $element): void
    {
        $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::TAG_ATTRIBUTES[$element->getName()] ?? []);
        foreach ($element->getAttributes() as $name => $value) {
            if (!in_array($name, $allowed, true)) {
                throw new ParseException(
                    "Attribute ".$name." is not allowed on ".$element->getName(),
                    $element->getPosition()
                );
            }
        }

        $this->validateStyle($element);
        $this->validateColor($element);
        $this->validateIntegers($element);
        $this->validateOneOf($element, "align");
        $this->validateOneOf($element, "vertical-align");
        $this->validateOneOf($element, "wrap");
        $this->validateOneOf($element, "overflow");
        $this->validateOneOf($element, "border");
    }

    private function validateStyle(ElementNode $element): void
    {
        $inlineStyle = $element->getAttribute("style");
        if (is_string($inlineStyle)) {
            foreach (explode(";", $inlineStyle) as $declaration) {
                $declaration = trim($declaration);
                if ($declaration === "") {
                    continue;
                }
                if (!preg_match('/^([a-z-]+)\s*:\s*(.+)$/i', $declaration, $matches)) {
                    throw new ParseException("Invalid style declaration: ".$declaration, $element->getPosition());
                }
                if (!in_array(strtolower($matches[1]), self::STYLE_PROPERTIES, true)) {
                    throw new ParseException("Unknown style property: ".$matches[1], $element->getPosition());
                }
                $this->validateStyleValue(strtolower($matches[1]), trim($matches[2]), $element);
            }
        }
    }

    private function validateStyleValue(string $name, string $value, ElementNode $element): void
    {
        if (in_array($name, ["color", "background"], true)) {
            try {
                $this->color->validate($value);
            } catch (Exception $exception) {
                throw new ParseException($exception->getMessage(), $element->getPosition());
            }
        }
        if ($name === "width" && !preg_match('/^(?:\d+|\d+(?:\.\d+)?%)$/', $value)) {
            throw new ParseException("Invalid style width: ".$value, $element->getPosition());
        }
        if (in_array($name, self::ATTRIBUTES_1, true) && !ctype_digit($value)) {
            throw new ParseException("Invalid numeric style value for ".$name.": ".$value, $element->getPosition());
        }
        $values = self::ANSI_STYLES;
        unset($values["border"]);
        if (isset($values[$name]) && !in_array(strtolower($value), $values[$name], true)) {
            throw new ParseException("Invalid style value for ".$name.": ".$value, $element->getPosition());
        }
    }

    private function validateOneOf(ElementNode $element, string $attribute): void
    {
        $value = $element->getAttribute($attribute);
        $styles = self::ANSI_STYLES[$attribute];
        if (is_string($value) && !in_array(strtolower($value), $styles, true)) {
            throw new ParseException(
                "Invalid value ".$value." for attribute ".$attribute,
                $element->getPosition()
            );
        }
    }

    private function validateStructure(ElementNode $element, ?ElementNode $parent): void
    {
        $parentName = $parent?->getName();
        $allowedParents = match ($element->getName()) {
            "thead", "tbody" => ["table"],
            "tr" => ["thead", "tbody"],
            "th" => ["tr"],
            "td" => ["tr"],
            "li" => ["ol", "ul"],
            "column" => ["columns", "table"],
            default => null
        };
        if ($allowedParents !== null && !in_array($parentName, $allowedParents, true)) {
            throw new ParseException(
                $element->getName()." is not allowed inside ".($parentName ?? "the document"),
                $element->getPosition()
            );
        }

        foreach (["link" => "href", "progress" => "value"] as $tag => $required) {
            if ($element->getName() === $tag && !is_string($element->getAttribute($required))) {
                throw new ParseException($tag." requires attribute ".$required, $element->getPosition());
            }
        }
        if ($element->getName() === "progress") {
            $value = $element->getAttribute("value");
            $max = $element->getAttribute("max", "100");
            if (!is_numeric($value) || !is_numeric($max) || (float) $max <= 0) {
                throw new ParseException("Progress value and max must be numeric and max must be positive", $element->getPosition());
            }
        }
        if ($element->getName() === "link" && preg_match('/[\x00-\x1F\x7F]/', (string) $element->getAttribute("href", ""))) {
            throw new ParseException("Link href contains control characters", $element->getPosition());
        }
    }

    private function validateColor(ElementNode $element): void
    {
        foreach (["color", "background"] as $attribute) {
            $value = $element->getAttribute($attribute);
            if ($value !== null) {
                if (!is_string($value)) {
                    throw new ParseException($attribute." requires a value", $element->getPosition());
                }
                try {
                    $this->color->validate($value);
                } catch (Exception $exception) {
                    throw new ParseException($exception->getMessage(), $element->getPosition());
                }
            }
        }
    }

    private function validateIntegers(ElementNode $element): void
    {
        $width = $element->getAttribute("width");
        if (is_string($width) && !preg_match('/^(?:\d+|\d+(?:\.\d+)?%)$/', $width)) {
            throw new ParseException("Invalid dimension for width: ".$width, $element->getPosition());
        }
        foreach (["min-width", "max-width"] as $attribute) {
            $value = $element->getAttribute($attribute);
            if (is_string($value) && !ctype_digit($value)) {
                throw new ParseException("Invalid dimension for ".$attribute.": ".$value, $element->getPosition());
            }
        }
        foreach (self::ATTRIBUTES_2 as $attribute) {
            $value = $element->getAttribute($attribute);
            if (is_string($value) && (!ctype_digit($value) || (int) $value < 0)) {
                throw new ParseException("Invalid integer for ".$attribute.": ".$value, $element->getPosition());
            }
        }
    }
}
