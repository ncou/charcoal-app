<?php

namespace Charcoal\App\Route;

// Dependencies from `PHP`
use InvalidArgumentException;

// PSR-7 (http messaging) dependencies
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// Dependencies from `pimple`
use Pimple\Container;

// From `charcoal-config`
use Charcoal\Config\ConfigurableInterface;
use Charcoal\Config\ConfigurableTrait;

// Intra-module (`charcoal-app`) dependencies
use Charcoal\App\Route\RouteInterface;
use Charcoal\App\Route\ScriptRouteConfig;
use Charcoal\App\Script\ScriptInterface;

/**
 * Script Route Handler.
 */
class ScriptRoute implements
    ConfigurableInterface,
    RouteInterface
{
    use ConfigurableTrait;

    /**
     * Create new script route (CLI)
     *
     * ### Dependencies
     *
     * **Required**
     *
     * - `config` — ScriptRouteConfig
     *
     * **Optional**
     *
     * - `logger` — PSR-3 Logger
     *
     * @param array $data Dependencies.
     */
    public function __construct(array $data)
    {
        $this->setConfig($data['config']);
    }

    /**
     * ConfigurableTrait > create_config()
     *
     * @param mixed|null $data Optional config data.
     * @return ScriptRouteConfig
     */
    public function createConfig($data = null)
    {
        return new ScriptRouteConfig($data);
    }

    /**
     * @param Container         $container A dependencies container.
     * @param RequestInterface  $request   A PSR-7 compatible Request instance.
     * @param ResponseInterface $response  A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function __invoke(Container $container, RequestInterface $request, ResponseInterface $response)
    {
        $config = $this->config();

        $scriptController = $config['controller'];

        $scriptFactory = $container['script/factory'];

        $script = $scriptFactory->create($scriptController);

        $script->setData($config['script_data']);

        // Run (invoke) script.
        return $script($request, $response);
    }
}
