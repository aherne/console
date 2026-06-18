<?php

namespace Lucinda\Console\Styling;

final class Theme
{
    /** @var array<string,Style> */
    private array $styles = [];

    public function __construct()
    {
        $this->styles = [
            "h1" => new Style(["bold" => true, "color" => "bright-cyan", "margin-bottom" => 1]),
            "h2" => new Style(["bold" => true, "color" => "cyan", "margin-bottom" => 1]),
            "h3" => new Style(["bold" => true]),
            "strong" => new Style(["bold" => true]),
            "b" => new Style(["bold" => true]),
            "em" => new Style(["italic" => true]),
            "i" => new Style(["italic" => true]),
            "u" => new Style(["underline" => true]),
            "s" => new Style(["strikethrough" => true]),
            "code" => new Style(["color" => "bright-yellow"]),
            "kbd" => new Style(["inverse" => true]),
            "success" => new Style(["color" => "bright-green", "bold" => true]),
            "info" => new Style(["color" => "bright-cyan"]),
            "warning" => new Style(["color" => "bright-yellow", "bold" => true]),
            "error" => new Style(["color" => "bright-red", "bold" => true]),
            "badge" => new Style(["inverse" => true, "bold" => true]),
            "muted" => new Style(["dim" => true]),
            "danger" => new Style(["color" => "bright-red", "bold" => true]),
            "page-title" => new Style(["color" => "bright-cyan", "bold" => true, "margin-bottom" => 1]),
        ];
    }

    /**
     * @param array<string,int|string|bool|null> $properties
     */
    public function define(string $name, array $properties): self
    {
        $clone = clone $this;
        $clone->styles[$name] = new Style($properties);
        return $clone;
    }

    public function get(string $name): Style
    {
        return $this->styles[$name] ?? new Style();
    }

    public function withLightColors(): self
    {
        return $this
            ->define("h1", ["bold" => true, "color" => "blue", "margin-bottom" => 1])
            ->define("h2", ["bold" => true, "color" => "magenta", "margin-bottom" => 1]);
    }

    public function withoutColors(): self
    {
        return $this
            ->define("h1", ["bold" => true, "underline" => true, "margin-bottom" => 1])
            ->define("h2", ["bold" => true, "margin-bottom" => 1])
            ->define("success", ["bold" => true])
            ->define("info", [])
            ->define("warning", ["bold" => true])
            ->define("error", ["bold" => true, "inverse" => true]);
    }

    public function withHighContrast(): self
    {
        return $this
            ->define("success", ["color" => "bright-green", "background" => "black", "bold" => true])
            ->define("info", ["color" => "bright-cyan", "background" => "black", "bold" => true])
            ->define("warning", ["color" => "black", "background" => "bright-yellow", "bold" => true])
            ->define("error", ["color" => "bright-white", "background" => "red", "bold" => true]);
    }
}
