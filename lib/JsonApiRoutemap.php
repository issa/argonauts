<?php

namespace Argonauts;

use Argonauts\Contracts\JsonApiPlugin;
use Argonauts\Middlewares\Authentication;
use Argonauts\Middlewares\JsonApi as JsonApiMiddleware;
use Argonauts\Providers\StudipServices;
use Argonauts\Routes\AuthenticatedExample;
use Argonauts\Routes\UnauthenticatedExample;
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
 * JsonApiRoutemap::authenticatedRoutes vermerkt. Kernrouten ohne
 * notwendige Autorisierung werden in
 * JsonApiRoutemap::unauthenticatedRoutes registriert. Routen aus Plugins
 * werden jeweils in den Methoden
 * \Argonauts\Contracts\JsonApiPlugin::registerAuthenticatedRoutes und
 * \Argonauts\Contracts\JsonApiPlugin::registerUnauthenticatedRoutes
 * eingetragen.
 *
 * Zu authentifizierende Routen werden in \Argonauts\Middlewares\Authentication
 * authentifiziert.
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
 * @see \Argonauts\Middlewares\Authentication
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
     * \Argonauts\Middlewares\Authentication ausgestattet und in
     * JsonApiRoutemap::authenticatedRoutes eingetragen. Routen ohne
     * Autorisierung werden in JsonApiRoutemap::unauthenticatedRoutes vermerkt.
     */
    public function __invoke()
    {
        $this->app->add(new JsonApiMiddleware($this->app));

        $this->app->group('', [$this, 'authenticatedRoutes'])
            ->add(new Authentication($this->getAuthenticator()));
        $this->app->group('', [$this, 'unauthenticatedRoutes']);
    }

    /**
     * Hier werden autorisierte (Kern-)Routen explizit vermerkt.
     * Außerdem wird über die \PluginEngine allen JsonApiPlugins die
     * Möglichkeit gegeben, sich hier einzutragen.
     */
    public function authenticatedRoutes()
    {
        \PluginEngine::sendMessage(JsonApiPlugin::class, 'registerAuthenticatedRoutes', $this->app);

        $this->app->get('/auth', AuthenticatedExample::class);
        $this->app->get('/users', UsersIndex::class);
        $this->app->post('/user/{id}', UserUpdate::class);
    }

    /**
     * Hier werden unautorisierte (Kern-)Routen explizit vermerkt.
     * Außerdem wird über die \PluginEngine allen JsonApiPlugins die
     * Möglichkeit gegeben, sich hier einzutragen.
     */
    public function unauthenticatedRoutes()
    {
        \PluginEngine::sendMessage(JsonApiPlugin::class, 'registerUnauthenticatedRoutes', $this->app);

        $this->app->get('/unauth', UnauthenticatedExample::class);
    }

    private function getAuthenticator()
    {
        $container = $this->app->getContainer();

        return $container[StudipServices::AUTHENTICATOR];
    }
}
