<?php

namespace Argonauts\Test;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;

class SimpleRoute extends JsonApiController
{
    public function index(Request $request, Response $response, $args)
    {
        $user = \User::find('76ed43ef286fb55cf9e41beadb484a9f');

        return $this->getContentResponse($user);
    }

    public function create(Request $request, Response $response, $args)
    {
        $newUser = \User::create(
            [
                'username' => 'new@user',
                'last_name' => 'User',
                'first_name' => 'First',
            ]
        );

        return $this->getCreatedResponse($newUser);
    }

    public function destroy(Request $request, Response $response, $args)
    {
        return $this->getCodeResponse(200);
    }
}
