<?php

require_once 'composer_modules/autoload.php';

use \Argonauts\AppFactory;

/**
 * ArgonautsPlugin.class.php.
 *
 * ...
 *
 * @author  Issa <issa@luniki.de>
 *
 * @version 0.1.0
 */
class ArgonautsPlugin extends \StudIPPlugin implements \SystemPlugin
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function onEnable($id)
    {
        // enable nobody role by default
        \RolePersistence::assignPluginRoles($id, array(7));
    }

    public static function onDisable($id)
    {
    }

    public function perform($unconsumed_path)
    {
        $appFactory = new AppFactory();
        $app = $appFactory->makeApp($this);

        $app->group('/argonautsplugin', new \Argonauts\JsonApi($app, $this));

        $app->run();
    }
}
