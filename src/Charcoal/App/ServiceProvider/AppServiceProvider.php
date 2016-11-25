<?php

namespace Charcoal\App\ServiceProvider;

// Dependencies from Pimple
use \Pimple\ServiceProviderInterface;
use \Pimple\Container;

// Dependencies from Slim
use \Slim\Http\Uri;

// Dependencies from charcoal-factory
use \Charcoal\Factory\GenericFactory as Factory;

// Intra-module (`charcoal-app`) dependencies
use \Charcoal\App\Action\ActionInterface;
use \Charcoal\App\Script\ScriptInterface;
use \Charcoal\App\Route\RouteInterface;
use \Charcoal\App\Module\ModuleInterface;

use \Charcoal\App\Handler\Error;
use \Charcoal\App\Handler\PhpError;
use \Charcoal\App\Handler\Shutdown;
use \Charcoal\App\Handler\NotAllowed;
use \Charcoal\App\Handler\NotFound;

use \Charcoal\App\Template\TemplateInterface;
use \Charcoal\App\Template\TemplateBuilder;
use \Charcoal\App\Template\WidgetInterface;
use \Charcoal\App\Template\WidgetBuilder;

/**
 * Application Service Provider
 *
 * Configures Charcoal and Slim and provides various Charcoal services to a container.
 *
 * ## Services
 * - `logger` `\Psr\Log\Logger`
 *
 * ## Helpers
 * - `logger/config` `\Charcoal\App\Config\LoggerConfig`
 *
 * ## Requirements / Dependencies
 * - `config` A `ConfigInterface` must have been previously registered on the container.
 */
class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container A container instance.
     * @return void
     */
    public function register(Container $container)
    {
        $this->registerHandlerServices($container);
        $this->registerRouteServices($container);
        $this->registerRequestControllerServices($container);
        $this->registerScriptServices($container);
        $this->registerModuleServices($container);
    }

    /**
     * @param Container $container The DI container.
     * @return void
     */
    protected function registerHandlerServices(Container $container)
    {
        $config = $container['config'];

        if (!isset($container['base-url'])) {
            /**
             * Base URL as a PSR-7 UriInterface object for the current request
             * or the Charcoal application.
             *
             * @param Container $container
             * @return \Psr\Http\Message\UriInterface
             */
            $container['base-url'] = function (Container $container) {
                if (isset($container['config']['base_url'])) {
                    $baseUrl = $container['config']['base_url'];
                } else {
                    $baseUrl = $container['request']->getUri()->getBaseUrl();
                }

                $baseUrl = Uri::createFromString($baseUrl)->withUserInfo('');

                return $baseUrl;
            };
        }

        if (!isset($config['handlers'])) {
            return;
        }

        /**
         * HTTP 404 (Not Found) handler.
         *
         * @param  object|HandlerInterface $handler   An error handler instance.
         * @param  Container               $container A container instance.
         * @return HandlerInterface
         */
        $container->extend('notFoundHandler', function ($handler, Container $container) use ($config) {
            if ($handler instanceof \Slim\Handlers\NotFound) {
                $handler = new NotFound($container);

                if (isset($config['handlers']['notFound'])) {
                    $handler->config()->merge($config['handlers']['notFound']);
                }

                $handler->init();
            }

            return $handler;
        });

        /**
         * HTTP 405 (Not Allowed) handler.
         *
         * @param  object|HandlerInterface $handler   An error handler instance.
         * @param  Container               $container A container instance.
         * @return HandlerInterface
         */
        $container->extend('notAllowedHandler', function ($handler, Container $container) use ($config) {
            if ($handler instanceof \Slim\Handlers\NotAllowed) {
                $handler = new NotAllowed($container);

                if (isset($config['handlers']['notAllowed'])) {
                    $handler->config()->merge($config['handlers']['notAllowed']);
                }

                $handler->init();
            }

            return $handler;
        });

        /**
         * HTTP 500 (Error) handler for PHP 7+ Throwables.
         *
         * @param  object|HandlerInterface $handler   An error handler instance.
         * @param  Container               $container A container instance.
         * @return HandlerInterface
         */
        $container->extend('phpErrorHandler', function ($handler, Container $container) use ($config) {
            if ($handler instanceof \Slim\Handlers\PhpError) {
                $handler = new PhpError($container);

                if (isset($config['handlers']['phpError'])) {
                    $handler->config()->merge($config['handlers']['phpError']);
                }

                $handler->init();
            }

            return $handler;
        });

        /**
         * HTTP 500 (Error) handler.
         *
         * @param  object|HandlerInterface $handler   An error handler instance.
         * @param  Container               $container A container instance.
         * @return HandlerInterface
         */
        $container->extend('errorHandler', function ($handler, Container $container) use ($config) {
            if ($handler instanceof \Slim\Handlers\Error) {
                $handler = new Error($container);

                if (isset($config['handlers']['error'])) {
                    $handler->config()->merge($config['handlers']['error']);
                }

                $handler->init();
            }

            return $handler;
        });

        if (!isset($container['shutdownHandler'])) {
            /**
             * HTTP 503 (Service Unavailable) handler.
             *
             * This handler is not part of Slim.
             *
             * @param  Container $container A container instance.
             * @return HandlerInterface
             */
            $container['shutdownHandler'] = function (Container $container) {
                $config  = $container['config'];
                $handler = new Shutdown($container);

                if (isset($config['handlers']['shutdown'])) {
                    $handler->config()->merge($config['handlers']['shutdown']);
                }

                return $handler->init();
            };
        }
    }

    /**
     * @param Container $container The DI container.
     * @return void
     */
    protected function registerRouteServices(Container $container)
    {
        /**
         * The Route Factory service is used to instanciate new routes.
         *
         * @param Container $container A container instance.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['route/factory'] = function (Container $container) {
            return new Factory([
                'base_class' => RouteInterface::class,
                'resolver_options' => [
                    'suffix' => 'Route'
                ],
                'arguments' => [[
                    'logger' => $container['logger']
                ]]
            ]);
        };
    }

    /**
     * @param Container $container The DI container.
     * @return void
     */
    protected function registerRequestControllerServices(Container $container)
    {
        /**
         * The Action Factory service is used to instanciate new actions.
         *
         * - Actions are `ActionInterface` and must be suffixed with `Action`.
         * - The container is passed to the created action constructor, which will call `setDependencies()`.
         *
         * @param Container $container A container instance.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['action/factory'] = function (Container $container) {
            return new Factory([
                'base_class' => ActionInterface::class,
                'resolver_options' => [
                    'suffix' => 'Action'
                ],
                'arguments' => [[
                    'container' => $container,
                    'logger' => $container['logger'],

                ]]
            ]);
        };

        /**
         * The Script Factory service is used to instanciate new scripts.
         *
         * - Scripts are `ScriptInterface` and must be suffixed with `Script`.
         * - The container is passed to the created script constructor, which will call `setDependencies()`.
         *
         * @param Container $container A container instance.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['script/factory'] = function (Container $container) {
            return new Factory([
                'base_class' => ScriptInterface::class,
                'resolver_options' => [
                    'suffix' => 'Script'
                ],
                'arguments' => [[
                    'container' => $container,
                    'logger' => $container['logger'],
                    'climate' => $container['climate'],
                    'climate_reader' => $container['climate/reader']
                ]]
            ]);
        };

        /**
         * The Template Factory service is used to instanciate new templates.
         *
         * - Templates are `TemplateInterface` and must be suffixed with `Template`.
         * - The container is passed to the created template constructor, which will call `setDependencies()`.
         *
         * @param Container $container A container instance.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['template/factory'] = function (Container $container) {
            return new Factory([
                'base_class' => TemplateInterface::class,
                'resolver_options' => [
                    'suffix' => 'Template'
                ],
                'arguments' => [[
                    'container' => $container,
                    'logger' => $container['logger']
                ]]
            ]);
        };

        /**
         * The Widget Factory service is used to instanciate new widgets.
         *
         * - Widgets are `WidgetInterface` and must be suffixed with `Widget`.
         * - The container is passed to the created widget constructor, which will call `setDependencies()`.
         *
         * @param Container $container A container instance.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['widget/factory'] = function (Container $container) {
            return new Factory([
                'base_class' => WidgetInterface::class,
                'resolver_options' => [
                    'suffix' => 'Widget'
                ],
                'arguments' => [[
                    'container' => $container,
                    'logger' => $container['logger']
                ]]
            ]);
        };
        /**
         * @param Container $container A container instance.
         * @return TemplateBuilder
         */
        $container['widget/builder'] = function (Container $container) {
            return new WidgetBuilder($container['widget/factory'], $container);
        };
    }

    /**
     * @param Container $container The DI container.
     * @return void
     */
    protected function registerModuleServices(Container $container)
    {
        /**
         * The Module Factory service is used to instanciate new modules.
         *
         * - Modules are `ModuleInterface` and must be suffixed with `Module`.
         *
         * @param Container $container A container instance.
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['module/factory'] = function (Container $container) {
            return new Factory([
                'base_class' => ModuleInterface::class,
                'resolver_options' => [
                    'suffix' => 'Module'
                ],
                'arguments' => [[
                    'logger' => $container['logger']
                ]]
            ]);
        };
    }

    /**
     * @param Container $container A container instance.
     * @return void
     */
    protected function registerScriptServices(Container $container)
    {
        /**
         * @param Container $container A container instance.
         * @return null|\League\CLImate\Util\Reader\ReaderInterface
         */
        $container['climate/reader'] = function (Container $container) {
            return null;
        };

        /**
         * @param Container $container A container instance.
         * @return CLImate
         */
        $container['climate'] = function (Container $container) {
            $climate = new CLImate();
            return $climate;
        };
    }
}
