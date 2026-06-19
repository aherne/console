<?php

namespace Lucinda\Console\Language;

/**
 * Common contract for parsed document nodes.
 */
interface Node
{
    /**
     * Returns the source position where the node begins.
     *
     * @return SourcePosition
     */
    public function getPosition(): SourcePosition;
}
