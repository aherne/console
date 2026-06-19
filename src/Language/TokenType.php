<?php

namespace Lucinda\Console\Language;

/**
 * Enumerates tokenizer output categories.
 */
enum TokenType
{
    case TEXT;
    case OPEN_TAG;
    case CLOSE_TAG;
    case SELF_CLOSING_TAG;
}
