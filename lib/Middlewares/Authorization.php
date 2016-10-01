<?php

namespace Argonauts\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Diese Klasse ist eine "leere" Middleware, die noch implementiert
 * werden muss.
 *
 * Allerdings wird sie jetzt schon in \Argonauts\JsonApiRoutemap
 * verwendet, um dort die autorisierten Routen abzusichern.
 *
 * @todo muss zu einem späteren Zeitpunk implementiert werden
 */
class Authorization
{
    /**
     * Der Konstruktor.
     *
     * @param \Slim\App $app die Slim-Applikation
     */
    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
        $this->plugin = $app->getContainer()['plugin'];
    }

    /**
     * Hier muss die Autorisierung implementiert werden.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  das
     *                                                           PSR-7 Request-Objekt
     * @param \Psr\Http\Message\ResponseInterface      $response das PSR-7
     *                                                           Response-Objekt
     * @param callable                                 $next     das nächste Middleware-Callable
     *
     * @return \Psr\Http\Message\ResponseInterface die neue Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        // TODO

        return $next($request, $response);
    }
}
