<?php

use Argonauts\AppFactory;
use Argonauts\JsonApiRoutemap;
use Argonauts\Providers\StudipServices;

/**
 * Mit Hilfe dieses Plugins kann eine JSON-API-kompatible
 * Schnittstelle für Stud.IP erstellt werden.
 *
 * Das Plugin tut nur zwei Sachen: Zum einen wird bei der Aktivierung
 * des Plugins dieses auch direkt für nicht eingeloggte Nutzer
 * (`nobody`) freigeschaltet und alle HTTP-Request, die an dieses
 * Plugin gehen (via plugins.php), werden an eine Slim-Applikation
 * delegiert, die von der AppFactory erstellt wird. Die
 * Slim-Applikation wird in der Klasse \Argonauts\JsonApiRoutemap mit
 * Routern ausgestattet.
 *
 * @see http://www.slimframework.com/
 * @see \Argonauts\AppFactory
 * @see \Argonauts\JsonApiRoutemap
 */
class ArgonautsPlugin extends \StudIPPlugin implements \SystemPlugin
{
    public function __construct()
    {
        parent::__construct();

        require_once 'composer_modules/autoload.php';
    }

    public static function onEnable($pluginId)
    {
        // enable nobody role by default
        \RolePersistence::assignPluginRoles($pluginId, array(7));
    }

    public function perform($unconsumedPath)
    {
        $appFactory = new AppFactory();
        $app = $appFactory->makeApp($this);

        $container = $app->getContainer();
        $container->register(new StudipServices());

        $app->group('/argonautsplugin', new JsonApiRoutemap($app, $this));

        $app->run();
    }
}
