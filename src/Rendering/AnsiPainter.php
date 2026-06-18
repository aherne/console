<?php

namespace Lucinda\Console\Rendering;

use Lucinda\Console\Styling\Style;
use Lucinda\Console\Terminal\EnvironmentDetector\Results;

final class AnsiPainter
{
    private const MAPPINGS = [
        "bold" => 1,
        "dim" => 2,
        "italic" => 3,
        "underline" => 4,
        "blink" => 5,
        "inverse" => 7,
        "hidden" => 8,
        "strikethrough" => 9
        ];
    private readonly Color $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function paint(string $text, Style $style, Results $environment, ?string $href = null): string
    {
        $codes = [];
        foreach (self::MAPPINGS as $property => $code) {
            if ($style->get($property) === true) {
                $codes[] = $code;
            }
        }
        foreach (["color" => false, "background" => true] as $property => $background) {
            $value = $style->get($property);
            if (is_string($value)) {
                $code = $this->color->getAnsiCode($value, $environment->getColorDepth(), $background);
                if ($code !== null) {
                    $codes[] = $code;
                }
            }
        }

        $output = $codes === [] ? $text : "\e[".implode(";", $codes)."m".$text."\e[0m";
        if ($href !== null && $environment->getHyperlinks()) {
            $safeHref = str_replace(["\e", "\a"], "", $href);
            $output = "\e]8;;".$safeHref."\e\\".$output."\e]8;;\e\\";
        }
        return $output;
    }
}
