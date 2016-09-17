<?php

namespace Argonauts;

use PluginEngine;
use Argonauts\JsonApiIntegration\Config\Config as C;

class JsonApiMiddleware
{
    public function __construct($app, $plugin)
    {
        $this->app = $app;
        $this->plugin = $plugin;
    }

    public function __invoke($req, $res, $next)
    {
        $container = $this->app->getContainer();

        $container[C::NAME] = [
            C::SCHEMAS => [
                \User::class => \Argonauts\Schema\User::class,
            ],
            C::JSON => [
                C::JSON_URL_PREFIX => rtrim(PluginEngine::getURL($this->plugin, [], ''), '/'),
            ],
        ];

        $container->register(new AppServiceProvider());

        $this->registerExceptionHandler($container);

        $response = $next($req, $res);

        return $response;
    }

    /**
     * Register exception handler.
     */
    protected function registerExceptionHandler($container)
    {
        $previousHandler = null;
        if ($container['errorHandler']) {
            $previousHandler = $container['errorHandler'];
        }

        unset($container['errorHandler']);

        $container['errorHandler'] = function ($container) use ($previousHandler) {
            return new JsonApiExceptionHandler($container, $previousHandler);
        };
    }
}
