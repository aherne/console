<?php

namespace Lucinda\Console;

/**
 * Encapsulates a bash text able to be styled
 */
class Text implements \Stringable
{
    private string $value;
    private ?FontStyle $fontStyle = null;
    private ?BackgroundColor $backgroundColor = null;
    private ?ForegroundColor $foregroundColor = null;

    /**
     * Sets text to style
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Sets text style (eg: makes it bold)
     *
     * @param FontStyle $fontStyle
     */
    public function setFontStyle(FontStyle $fontStyle): void
    {
        $this->fontStyle = $fontStyle;
    }

    /**
     * Sets text background color
     *
     * @param BackgroundColor $backgroundColor
     */
    public function setBackgroundColor(BackgroundColor $backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * Sets text foreground color
     *
     * @param ForegroundColor $foregroundColor
     */
    public function setForegroundColor(ForegroundColor $foregroundColor): void
    {
        $this->foregroundColor = $foregroundColor;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Stringable::__toString()
     */
    public function __toString(): string
    {
        if ($this->fontStyle || $this->backgroundColor || $this->foregroundColor) {
            return $this->getStyledValue();
        } else {
            return $this->getOriginalValue();
        }
    }

    /**
     * Gets original text
     *
     * @return string
     */
    public function getOriginalValue(): string
    {
        return $this->value;
    }

    /**
     * Gets styled text
     *
     * @return string
     */
    public function getStyledValue(): string
    {
        if (!$this->fontStyle && !$this->backgroundColor && !$this->foregroundColor) {
            return $this->value;
        }

        $styles = [];
        if ($this->fontStyle) {
            $styles[] = $this->fontStyle->value;
        }
        if ($this->foregroundColor) {
            $styles[] = $this->foregroundColor->value;
        }
        if ($this->backgroundColor) {
            $styles[] = $this->backgroundColor->value;
        }

        $openingSequence = "\e[".implode(";", $styles)."m";
        $value = str_replace("\e[0m", "\e[0m".$openingSequence, $this->value);

        return $openingSequence.$value."\e[0m";
    }
}
