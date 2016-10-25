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
class Authentication
{
    // a callable accepting two arguments username and password and
    // returning either null or a Stud.IP user object
    private $authenticator;

    /**
     * Der Konstruktor.
     *
     * @param callable $authenticator ein Callable, das den
     *                                Nutzernamen und das Passwort als Argumente erhält und damit
     *                                entweder einen Stud.IP-User-Objekt oder null zurückgibt
     */
    public function __construct($authenticator)
    {
        $this->authenticator = $authenticator;
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
        $user = null;
        $server_params = $request->getServerParams();
        $realm = 'Stud.IP JSON-API';

        // HTTP basic authentication
        if (isset($server_params['PHP_AUTH_USER'], $server_params['PHP_AUTH_PW'])) {
            $authenticator = $this->authenticator;
            $user = $authenticator(
                $server_params['PHP_AUTH_USER'],
                $server_params['PHP_AUTH_PW']
            );
        }

        // session auth
        elseif (isset($GLOBALS['auth'])
                 && $GLOBALS['auth']->is_authenticated()
                 && $GLOBALS['user']->id !== 'nobody') {
            $user = $GLOBALS['user'];
        }

        if ($user === null) {
            return $response
                ->withStatus(401)
                ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $realm));
        }

        // TODO: irgendwas mit $user_id machen

        /* Everything ok, call next middleware. */
        return $next($request, $response);
    }
}
