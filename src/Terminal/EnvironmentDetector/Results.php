<?php

namespace Lucinda\Console\Terminal\EnvironmentDetector;

use Lucinda\Console\Terminal\ColorDepth;

final class Results
{
    private int $width = 80;
    private int $height = 24;
    private ColorDepth $colorDepth = ColorDepth::ANSI16;
    private bool $unicode = true;
    private bool $hyperlinks = false;
    private bool $interactive = false;

    public function setWidth(int $value): void
    {
        $this->width = $value;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setHeight(int $value): void
    {
        $this->height = $value;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setColorDepth(ColorDepth $value): void
    {
        $this->colorDepth = $value;
    }

    public function getColorDepth(): ColorDepth
    {
        return $this->colorDepth;
    }

    public function setUnicode(bool $value): void
    {
        $this->unicode = $value;
    }

    public function getUnicode(): bool
    {
        return $this->unicode;
    }

    public function setHyperlinks(bool $value): void
    {
        $this->hyperlinks = $value;
    }

    public function getHyperlinks(): bool
    {
        return $this->hyperlinks;
    }

    public function setInteractive(bool $value): void
    {
        $this->interactive = $value;
    }

    public function getInteractive(): bool
    {
        return $this->interactive;
    }
}