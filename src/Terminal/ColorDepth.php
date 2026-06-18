<?php

namespace Lucinda\Console\Terminal;

enum ColorDepth: int
{
    case NONE = 0;
    case ANSI16 = 16;
    case ANSI256 = 256;
    case TRUE_COLOR = 16777216;
}
