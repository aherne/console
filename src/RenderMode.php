<?php

namespace Lucinda\Console;

/**
 * Selects whether rendering should preserve ANSI terminal features or plain text only.
 */
enum RenderMode: string
{
    case ANSI = "ansi";
    case PLAIN_TEXT = "plain";
}
