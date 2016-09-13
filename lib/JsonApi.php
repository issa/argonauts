<?php

namespace Argonauts;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class JsonApi {

    public function __construct(\Slim\App $app, \StudipPlugin $plugin)
    {
        $this->app = $app;
        $this->plugin = $plugin;
    }

    public function __invoke()
    {
        // authorized
        $this->app
            ->group('', [$this, 'authorizedRoutes'])
            ->add(function ($request, $response, $next) {
                // TODO: authorize me here
                return $next($request, $response);
            });

        // unauthorized
        $this->app->group('', [$this, 'unauthorizedRoutes']);

        // Register plugin routes
        \PluginEngine::sendMessage('Argonauts\\JsonApiPlugin', 'registerRoutes', $this->app);
    }

    public function authorizedRoutes()
    {
        $this->app->get('/auth', function (Request $request, Response $response, $args) {
            $response->getBody()->write("Hello, authorized user, from inside of " . __CLASS__);
            return $response;
        });
    }

    public function unauthorizedRoutes()
    {
        $this->app->get('/unauth', function (Request $request, Response $response, $args) {
            $response->getBody()->write("Hello, unauthorized user, from inside of " . __CLASS__);
            return $response;
        });
    }
}
