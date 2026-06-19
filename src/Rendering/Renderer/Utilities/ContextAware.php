<?php

namespace Lucinda\Console\Rendering\Renderer\Utilities;

/**
 * Base class for renderers that share a rendering context.
 */
abstract class ContextAware
{
    protected RenderingContext $context;

    /**
     * Stores the shared rendering context.
     *
     * @param RenderingContext $context
     * @return void
     */
    public function __construct(RenderingContext $context)
    {
        $this->context = $context;
    }
}
