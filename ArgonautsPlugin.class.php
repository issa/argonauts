<?php

require_once 'composer_modules/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * ArgonautsPlugin.class.php
 *
 * ...
 *
 * @author  Issa <issa@luniki.de>
 * @version 0.1.0
 */

class ArgonautsPlugin extends \StudIPPlugin implements \SystemPlugin {

    public function __construct() {
        parent::__construct();
    }

    static function onEnable($id)
    {
        // enable nobody role by default
        \RolePersistence::assignPluginRoles($id, array(7));
    }

    static function onDisable($id)
    {
    }

    public function perform($unconsumed_path) {

        $app = new \Slim\App();

        $c = $app->getContainer();
        $c['plugin'] = $plugin = $this;
        $c['settings']['displayErrorDetails'] = true;

#        __NAMESPACE__ .  '\controllers\ChatsController:index'

        $app->add(__CLASS__ . ':mw_removeTrailingSlashes');

        $app->group('/argonautsplugin', function () use ($app, $plugin) {

            $this->group('/jsonapi', new Argonauts\JsonApi($app, $plugin));

            $this->get('/dummy', function (Request $request, Response $response, $args) {
                $response->getBody()->write("Hello, dummy");
                return $response;
            });
        });

        $app->run();
    }

    function mw_removeTrailingSlashes(Reques $request, Response $response, $next) {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            $uri = $uri->withPath(substr($path, 0, -1));
            return $response->withRedirect((string) $uri, 301);
        }
        return $next($request, $response);
    }
}
