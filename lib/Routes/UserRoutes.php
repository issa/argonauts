<?php

namespace Argonauts\Routes;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Argonauts\JsonApiController;

class UserRoutes extends JsonApiController {

    public function index(Request $request, Response $response, $args)
    {
        $user = $GLOBALS['user']->getAuthenticatedUser();
        return $this->getContentResponse($user);

        #$response->getBody()->write("Hello, authorized user, from inside of " . __METHOD__);
        #return $response;
    }
}
