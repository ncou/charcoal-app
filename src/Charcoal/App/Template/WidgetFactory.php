<?php

namespace Charcoal\App\Template;

// Module `charcoal-factory` dependencies
use \Charcoal\Factory\ResolverFactory;

/**
 * The TemplateFactory creates Template objects
 */
class WidgetFactory extends ResolverFactory
{
    /**
     * @return string
     */
    public function base_class()
    {
        return '\Charcoal\App\Template\WidgetInterface';
    }

    /**
     * @return string
     */
    public function resolver_suffix()
    {
        return 'Widget';
    }
}