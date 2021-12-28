<?php
namespace Lucinda\Console;

/**
 * Enum encapsulating valid foreground colors for bash texts
 */
enum ForegroundColor : int
{
    case BLACK = 30;
    case RED = 31;
    case GREEN = 32;
    case ORANGE = 33;
    case BLUE = 34;
    case MAGENTA = 35;
    case CYAN = 36;
    case LIGHT_GRAY = 37;
    case DEFAULT = 39;
}
