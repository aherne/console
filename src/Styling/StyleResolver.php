<?php

namespace Lucinda\Console\Styling;

use Lucinda\Console\Language\ElementNode;
use Lucinda\Console\Language\ParseException;

/**
 * Resolves inherited, theme, class, attribute, and inline styles for elements.
 */
final class StyleResolver
{
    private const BOOLEAN_PROPERTIES = [
        "bold", "dim", "italic", "underline", "blink", "inverse", "hidden", "strikethrough"
    ];
    private const STYLE_PROPERTIES = [
        "color", "background", "bold", "dim", "italic", "underline", "blink", "inverse", "hidden",
        "strikethrough", "width", "min-width", "max-width", "align", "vertical-align", "wrap", "overflow",
        "indent", "padding", "margin", "margin-top", "margin-bottom"
    ];
    private const PROPERTIES_1 = ["color", "background", "align", "vertical-align", "wrap", "overflow"];
    private const PROPERTIES_2 = ["width", "min-width", "max-width", "indent", "padding", "margin", "margin-top", "margin-bottom"];

    private Theme $theme;

    /**
     * Creates a resolver backed by the given theme.
     *
     * @param Theme $theme
     * @return void
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Resolves the final style for an element.
     *
     * @param ElementNode $element
     * @param ?Style $inherited
     * @return Style
     * @throws \Lucinda\Console\Language\ParseException
     */
    public function resolve(ElementNode $element, ?Style $inherited = null): Style
    {
        $style = $inherited ?? new Style();
        $style = $style->merge($this->theme->get($element->getName()));

        $classes = preg_split('/\s+/', trim((string) $element->getAttribute("class", ""))) ?: [];
        foreach ($classes as $class) {
            if ($class !== "") {
                $style = $style->merge($this->theme->get($class));
            }
        }

        return $style->merge(new Style($this->getProperties($element)));
    }

    /**
     * Extracts style properties declared directly on an element.
     *
     * @return array<string,int|string|bool|null>
     * @param ElementNode $element
     * @throws \Lucinda\Console\Language\ParseException
     */
    private function getProperties(ElementNode $element): array
    {
        $properties = [];
        foreach ($element->getAttributes() as $name => $value) {
            if (in_array($name, self::BOOLEAN_PROPERTIES, true)) {
                $properties[$name] = $value === true || in_array(strtolower((string) $value), ["1", "true", "yes"], true);
            } elseif (in_array($name, self::PROPERTIES_1, true)) {
                $properties[$name] = $value;
            } elseif (in_array($name, self::PROPERTIES_2, true)) {
                $properties[$name] = $value;
            }
        }

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
                $name = strtolower($matches[1]);
                if (!in_array($name, self::STYLE_PROPERTIES, true)) {
                    throw new ParseException("Unknown style property: ".$name, $element->getPosition());
                }
                $value = trim($matches[2]);
                $properties[$name] = in_array($name, self::BOOLEAN_PROPERTIES, true)
                    ? in_array(strtolower($value), ["1", "true", "yes"], true)
                    : $value;
            }
        }
        return $properties;
    }
}
