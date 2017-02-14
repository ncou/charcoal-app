<?php

namespace Charcoal\App\Route;

// Local namespace dependencies
use Charcoal\App\Route\RouteConfig;

/**
 *
 */
class ScriptRouteConfig extends RouteConfig
{
    /**
     * @var array $scriptData
     */
    private $scriptData = [];

    /**
     * Set the action data.
     *
     * @param array $scriptData The route data.
     * @return ActionRouteConfig Chainable
     */
    public function setScriptData(array $scriptData)
    {
        $this->scriptData = $scriptData;
        return $this;
    }

    /**
     * Get the action data.
     *
     * @return array
     */
    public function scriptData()
    {
        return $this->scriptData;
    }
}
