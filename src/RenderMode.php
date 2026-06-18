<?php

namespace Lucinda\Console;

enum RenderMode: string
{
    case ANSI = "ansi";
    case PLAIN_TEXT = "plain";
}
