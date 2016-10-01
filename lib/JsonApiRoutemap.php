<?php

namespace Argonauts;

use Argonauts\Contracts\JsonApiPlugin;
use Argonauts\Middlewares\Authorization;
use Argonauts\Middlewares\JsonApi as JsonApiMiddleware;
use Argonauts\Routes\AuthorizedExample;
use Argonauts\Routes\UnauthorizedExample;
use Argonauts\Routes\UsersIndex;
use Argonauts\Routes\UserUpdate;

/**
 * Diese Klasse ist die JSON-API-Routemap, in der alle Routen
 * registriert werden und die Middleware hinzugefügt wird, die
 * JSON-API spezifische Fehlerbehandlung usw. übernimmt.
 *
 * Routen der Kernklasen sind hier explizit vermerkt.
 *
 * Routen aus Plugins werden über die PluginEngine abgefragt. Plugins
 * können genau dann eigene Routen registrieren, wenn sie das
 * Interface \Argonauts\Contracts\JsonApiPlugin implementieren.
 *
 * Routen können entweder mit Autorisierung oder auch ohne eingetragen
 * werden. Autorisierte Kernrouten werden in
 * JsonApiRoutemap::authorizedRoutes vermerkt. Kernrouten ohne
 * notwendige Autorisierung werden in
 * JsonApiRoutemap::unauthorizedRoutes registriert. Routen aus Plugins
 * werden jeweils in den Methoden
 * \Argonauts\Contracts\JsonApiPlugin::registerAuthorizedRoutes und
 * \Argonauts\Contracts\JsonApiPlugin::registerUnauthorizedRoutes
 * eingetragen.
 *
 * Autorisierte Routen werden in \Argonauts\Middlewares\Authorization
 * autorisiert.
 *
 * Wie Routen registriert werden, kann man im `User Guide` des
 * Slim-Frameworks nachlesen
 * (http://www.slimframework.com/docs/objects/router.html#how-to-create-routes)
 *
 * Route-Handler können als Funktionen, in der Slim-Syntax
 * "Klassenname:Methodenname" oder auch mit dem Klassennamen einer
 * Klasse, die __invoke implementiert, angegeben werden. Die
 * __invoke-Variante wird hier sehr empfohlen.
 *
 * Beispiel:
 *
 *   use Studip\MeineRoute;
 *
 *   $this->app->post('/article/{id}/comments', MeineRoute::class);
 *
 *
 * @see \Argonauts\Middlewares\JsonApi
 * @see \Argonauts\Middlewares\Authorization
 * @see \Argonauts\Contracts\JsonApiPlugin
 * @see http://www.slimframework.com/docs/objects/router.html#how-to-create-routes
 */
class JsonApiRoutemap
{
    /**
     * Der Konstruktor.
     *
     * @param \Slim\App     $app    die Slim-Applikation, in der die Routen
     *                              definiert werden sollen
     * @param \StudipPlugin $plugin das Stud.IP-Plugin, in der die
     *                              Slim-Applikation läuft
     */
    public function __construct(\Slim\App $app, \StudipPlugin $plugin)
    {
        $this->app = $app;
        $this->plugin = $plugin;
    }

    /**
     * Hier werden die Routen tatsächlich eingetragen.
     * Autorisierte Routen werden mit der Middleware
     * \Argonauts\Middlewares\Authorization ausgestattet und in
     * JsonApiRoutemap::authorizedRoutes eingetragen. Routen ohne
     * Autorisierung werden in JsonApiRoutemap::unauthorizedRoutes vermerkt.
     */
    public function __invoke()
    {
        $this->app->add(new JsonApiMiddleware($this->app));

        $this->app->group('', [$this, 'authorizedRoutes'])->add(new Authorization($this->app, $this->plugin));
        $this->app->group('', [$this, 'unauthorizedRoutes']);
    }

    /**
     * Hier werden autorisierte (Kern-)Routen explizit vermerkt.
     * Außerdem wird über die \PluginEngine allen JsonApiPlugins die
     * Möglichkeit gegeben, sich hier einzutragen.
     */
    public function authorizedRoutes()
    {
        \PluginEngine::sendMessage(JsonApiPlugin::class, 'registerAuthorizedRoutes', $this->app);

        $this->app->get('/auth', AuthorizedExample::class);
        $this->app->get('/users', UsersIndex::class);
        $this->app->post('/user/{id}', UserUpdate::class);
    }

    /**
     * Hier werden unautorisierte (Kern-)Routen explizit vermerkt.
     * Außerdem wird über die \PluginEngine allen JsonApiPlugins die
     * Möglichkeit gegeben, sich hier einzutragen.
     */
    public function unauthorizedRoutes()
    {
        \PluginEngine::sendMessage(JsonApiPlugin::class, 'registerUnauthorizedRoutes', $this->app);

        $this->app->get('/unauth', UnauthorizedExample::class);
    }
}
