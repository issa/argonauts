<?php

namespace Argonauts\Middlewares;

class Authorization
{
    public function __construct($app, $plugin)
    {
        $this->app = $app;
        $this->plugin = $plugin;
    }

    public function __invoke($request, $response, $next)
    {
        // TODO

        return $next($request, $response);
    }
}
