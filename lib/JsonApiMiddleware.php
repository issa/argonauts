<?php

namespace Argonauts;

use Argonauts\JsonApiIntegration\Config\Config as C;

class JsonApiMiddleware
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->plugin = $app->getContainer()['plugin'];
    }

    public function __invoke($req, $res, $next)
    {
        $container = $this->app->getContainer();

        $container->register(new JsonApiConfigProvider($this->plugin));
        $container->register(new JsonApiServiceProvider());

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