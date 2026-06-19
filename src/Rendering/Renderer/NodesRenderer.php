<?php

namespace Lucinda\Console\Rendering\Renderer;

/**
 * Renders a list of parsed nodes into terminal lines.
 */
interface NodesRenderer
{
    /**
     * Renders nodes within the given width.
     *
     * @param \Lucinda\Console\Language\Node[] $nodes
     *
     * @return string[]
     * @param int $width
     */
    function render(array $nodes, int $width): array;
}
