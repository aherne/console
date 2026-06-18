<?php

namespace Lucinda\Console\Rendering\Renderer;

interface NodesRenderer
{
    function render(array $nodes, int $width): array;
}