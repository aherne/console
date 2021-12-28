<?php
namespace Lucinda\Console;

/**
 * Enum encapsulating bash text styles
 */
enum FontStyle: int
{
    case NORMAL = 0;
    case BOLD = 1;
    case TRANSPARENT = 2;
    case ITALIC = 3;
    case UNDERLINE = 4;
    case BLINK = 5;
}
