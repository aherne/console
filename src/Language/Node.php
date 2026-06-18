<?php

namespace Lucinda\Console\Language;

interface Node
{
    public function getPosition(): SourcePosition;
}
