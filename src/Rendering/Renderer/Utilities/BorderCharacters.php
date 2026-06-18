<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

final class BorderCharacters extends ContextAware
{
    /** @return array{string,string,string,string,string,string,string} */
    public function borderCharacters(string $border): array
    {
        if (!$this->context->getEnvironment()->getUnicode() || $border === "ascii") {
            return ["+", "+", "+", "+", "-", "|", "+"];
        }
        return match ($border) {
            "double" => ["\u{2554}", "\u{2557}", "\u{255A}", "\u{255D}", "\u{2550}", "\u{2551}", "\u{256C}"],
            "rounded" => ["\u{256D}", "\u{256E}", "\u{2570}", "\u{256F}", "\u{2500}", "\u{2502}", "\u{253C}"],
            "heavy" => ["\u{250F}", "\u{2513}", "\u{2517}", "\u{251B}", "\u{2501}", "\u{2503}", "\u{254B}"],
            default => ["\u{250C}", "\u{2510}", "\u{2514}", "\u{2518}", "\u{2500}", "\u{2502}", "\u{253C}"]
        };
    }  

    /** @return array{string,string,string,string,string,string,string,string,string,string,string} */
    public function tableBorderCharacters(string $border): array
    {
        if (!$this->context->getEnvironment()->getUnicode() || $border === "ascii") {
            return ["+", "+", "+", "|", "-", "+", "+", "+", "+", "+", "+"];
        }
        return match ($border) {
            "double" => ["\u{2554}", "\u{2566}", "\u{2557}", "\u{2551}", "\u{2550}", "\u{2560}", "\u{256C}", "\u{2563}", "\u{255A}", "\u{2569}", "\u{255D}"],
            "heavy" => ["\u{250F}", "\u{2533}", "\u{2513}", "\u{2503}", "\u{2501}", "\u{2523}", "\u{254B}", "\u{252B}", "\u{2517}", "\u{253B}", "\u{251B}"],
            "rounded" => ["\u{256D}", "\u{252C}", "\u{256E}", "\u{2502}", "\u{2500}", "\u{251C}", "\u{253C}", "\u{2524}", "\u{2570}", "\u{2534}", "\u{256F}"],
            default => ["\u{250C}", "\u{252C}", "\u{2510}", "\u{2502}", "\u{2500}", "\u{251C}", "\u{253C}", "\u{2524}", "\u{2514}", "\u{2534}", "\u{2518}"]
        };
    }
}