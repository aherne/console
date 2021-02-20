<?php
namespace Lucinda\ConsoleTable;

/**
 * Encapsulates a bash text able to be styled
 */
class Text
{
    private $value;
    private $fontStyle;
    private $backgroundColor;
    private $foregroundColor;
    
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
    public function setFontStyle(int $fontStyle): void
    {
        $this->fontStyle = $fontStyle;
    }
    
    /**
     * Sets text background color
     *
     * @param BackgroundColor $backgroundColor
     */
    public function setBackgroundColor(int $backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }
    
    /**
     * Sets text foreground color
     *
     * @param ForegroundColor $foregroundColor
     */
    public function setForegroundColor(int $foregroundColor): void
    {
        $this->foregroundColor = $foregroundColor;
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
        $style = $this->fontStyle?$this->fontStyle:0;
        $color = $this->backgroundColor?$this->backgroundColor:($this->foregroundColor?$this->foregroundColor:1);
        return "\e[".$style.";".$color."m".$this->value."\e[0m";
    }
}
