<?php

namespace Lucinda\Console;

/**
 * Enum encapsulating valid background colors for bash texts
 */
enum BackgroundColor : int
{
    case BLACK = 40;
    case RED = 41;
    case GREEN = 42;
    case ORANGE = 43;
    case BLUE = 44;
    case MAGENTA = 45;
    case CYAN = 46;
    case LIGHT_GRAY = 47;
    case DEFAULT = 49;
    case DARK_GRAY = 100;
    case LIGHT_RED = 101;
    case LIGHT_GREEN = 102;
    case YELLOW = 103;
    case LIGHT_BLUE = 104;
    case LIGHT_PURPLE = 105;
    case TEAL = 106;
    case WHITE = 107;
}
