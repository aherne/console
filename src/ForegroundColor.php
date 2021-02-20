<?php
namespace Lucinda\ConsoleTable;

/**
 * Enum encapsulating valid foreground colors for bash texts
 */
interface ForegroundColor
{
    const BLACK = 30;
    const RED = 31;
    const GREEN = 32;
    const ORANGE = 33;
    const BLUE = 34;
    const MAGENTA = 35;
    const CYAN = 36;
    const LIGHT_GRAY = 37;
    const DEFAULT = 39;
}
