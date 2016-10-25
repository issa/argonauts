<?php

namespace Argonauts\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;

class UnauthenticatedExample extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $response->getBody()->write('Hello, unauthenticated user, from inside of '.__CLASS__);

        return $response;
    }
}
