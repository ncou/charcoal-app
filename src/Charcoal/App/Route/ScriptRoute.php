<?php

namespace Charcoal\App\Route;

// Dependencies from `PHP`
use \InvalidArgumentException;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// From `charcoal-config`
use \Charcoal\Config\ConfigInterface;
use \Charcoal\Config\ConfigurableInterface;
use \Charcoal\Config\ConfigurableTrait;

// Intra-module (`charcoal-app`) dependencies
use \Charcoal\App\AppAwareInterface;
use \Charcoal\App\AppAwareTrait;
use \Charcoal\App\AppInterface;
use \Charcoal\App\LoggerAwareInterface;
use \Charcoal\App\LoggerAwareTrait;
use \Charcoal\App\Route\RouteInterface;
use \Charcoal\App\Route\ScriptRouteConfig;
use \Charcoal\App\Script\ScriptFactory;

/**
 *
 */
class ScriptRoute implements
    AppAwareInterface,
    ConfigurableInterface,
    LoggerAwareInterface,
    RouteInterface
{
    use AppAwareTrait;
    use ConfigurableTrait;
    use LoggerAwareTrait;

    /**
     * Create new script route (CLI)
     *
     * ### Dependencies
     *
     * **Required**
     *
     * - `config` — ScriptRouteConfig
     * - `app`    — AppInterface
     *
     * **Optional**
     *
     * - `logger` — PSR-3 Logger
     *
     * @param array $data Dependencies.
     */
    public function __construct(array $data)
    {
        $this->set_config($data['config']);
        $this->set_app($data['app']);
        $this->set_logger($data['logger']);
    }

    /**
     * ConfigurableTrait > create_config()
     *
     * @param mixed|null $data Optional config data.
     * @return ConfigInterface
     */
    public function create_config($data = null)
    {
        return new ScriptRouteConfig($data);
    }

    /**
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        $config = $this->config();

        $script_ident = $config['ident'];
        $script_controller = $config['controller'];

        $script_factory = new ScriptFactory();
        $script = $script_factory->create($script_ident, [
            'app' => $this->app(),
            'logger' => $this->logger()
        ]);

        $script->set_data($config['script_data']);

        // Run (invoke) script.
        return $script($request, $response);
    }
}
