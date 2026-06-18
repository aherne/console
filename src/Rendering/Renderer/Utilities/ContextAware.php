<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

abstract class ContextAware
{
    protected RenderingContext $context;

    public function __construct(RenderingContext $context)
    {
        $this->context = $context;
    }
}