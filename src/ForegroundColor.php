<?php
namespace Lucinda\Console;

/**
 * Enum encapsulating valid foreground colors for bash texts
 */
interface ForegroundColor
{
    public const BLACK = 30;
    public const RED = 31;
    public const GREEN = 32;
    public const ORANGE = 33;
    public const BLUE = 34;
    public const MAGENTA = 35;
    public const CYAN = 36;
    public const LIGHT_GRAY = 37;
    public const DEFAULT = 39;
}
