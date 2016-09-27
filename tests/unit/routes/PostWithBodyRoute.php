<?php

namespace Argonauts\Test;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;

class PostWithBodyRoute extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        var_dump( $this->getDocument($request) );exit;

        return $this->getContentResponse($user);
    }
}
