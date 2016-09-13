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
        $this->app->get('', function (Request $request, Response $response, $args) {
            $response->getBody()->write("Hello, dummy from inside of " . __CLASS__);
            return $response;
        });


        // Register plugin routes
        \PluginEngine::sendMessage('Argonauts\\JsonApiPlugin', 'registerRoutes', $this->app);
    }
}
