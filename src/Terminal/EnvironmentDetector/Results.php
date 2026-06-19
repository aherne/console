<?php

namespace Lucinda\Console\Terminal\EnvironmentDetector;

use Lucinda\Console\Terminal\ColorDepth;

/**
 * Mutable terminal capability snapshot used by renderers.
 */
final class Results
{
    private int $width = 80;
    private int $height = 24;
    private ColorDepth $colorDepth = ColorDepth::ANSI16;
    private bool $unicode = true;
    private bool $hyperlinks = false;
    private bool $interactive = false;

    /**
     * Sets the terminal width in display columns.
     *
     * @param int $value
     * @return void
     */
    public function setWidth(int $value): void
    {
        $this->width = $value;
    }

    /**
     * Returns the terminal width in display columns.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Sets the terminal height in rows.
     *
     * @param int $value
     * @return void
     */
    public function setHeight(int $value): void
    {
        $this->height = $value;
    }

    /**
     * Returns the terminal height in rows.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Sets the detected color depth.
     *
     * @param ColorDepth $value
     * @return void
     */
    public function setColorDepth(ColorDepth $value): void
    {
        $this->colorDepth = $value;
    }

    /**
     * Returns the detected color depth.
     *
     * @return ColorDepth
     */
    public function getColorDepth(): ColorDepth
    {
        return $this->colorDepth;
    }

    /**
     * Sets whether unicode drawing characters should be used.
     *
     * @param bool $value
     * @return void
     */
    public function setUnicode(bool $value): void
    {
        $this->unicode = $value;
    }

    /**
     * Returns whether unicode drawing characters should be used.
     *
     * @return bool
     */
    public function getUnicode(): bool
    {
        return $this->unicode;
    }

    /**
     * Sets whether OSC 8 hyperlinks should be emitted.
     *
     * @param bool $value
     * @return void
     */
    public function setHyperlinks(bool $value): void
    {
        $this->hyperlinks = $value;
    }

    /**
     * Returns whether OSC 8 hyperlinks should be emitted.
     *
     * @return bool
     */
    public function getHyperlinks(): bool
    {
        return $this->hyperlinks;
    }

    /**
     * Sets whether output is attached to an interactive terminal.
     *
     * @param bool $value
     * @return void
     */
    public function setInteractive(bool $value): void
    {
        $this->interactive = $value;
    }

    /**
     * Returns whether output is attached to an interactive terminal.
     *
     * @return bool
     */
    public function getInteractive(): bool
    {
        return $this->interactive;
    }
}
