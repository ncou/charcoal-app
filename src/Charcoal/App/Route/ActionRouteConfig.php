<?php

namespace Charcoal\App\Route;

// Local namespace dependencies
use \Charcoal\App\Route\RouteConfig;

/**
 *
 */
class ActionRouteConfig extends RouteConfig
{
   
    /**
     * @var array $action_data
     */
    private $action_data = [];

    /**
     * Set the action data.
     *
     * @param array $action_data The route data.
     * @return ActionRouteConfig Chainable
     */
    public function set_action_data(array $action_data)
    {
        $this->action_data = $action_data;
        return $this;
    }

    /**
     * Get the action data.
     *
     * @return array
     */
    public function action_data()
    {
        return $this->action_data;
    }
}