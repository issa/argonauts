<?php

require_once 'composer_modules/autoload.php';

use \Argonauts\AppFactory;
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
        $appFactory = new AppFactory();
        $app = $appFactory->makeApp($this);

        $app->group('/argonautsplugin', new \Argonauts\JsonApi($app, $this));

        $app->run();
    }
}
