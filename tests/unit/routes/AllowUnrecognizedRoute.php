<?php

namespace Argonauts\Test;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;

class AllowUnrecognizedRoute extends JsonApiController
{
    protected $allowUnrecognizedParams = true;

    public function index(Request $request, Response $response, $args)
    {
        $user = \User::find('76ed43ef286fb55cf9e41beadb484a9f');

        return $this->getContentResponse($user);
    }
}
