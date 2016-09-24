<?php

namespace Argonauts\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;

class UserRoutes extends JsonApiController
{
    protected $allowedIncludePaths = ['contacts'];

    public function index(Request $request, Response $response, $args)
    {
        //throw new \RuntimeException();
        //throw new \Neomerx\JsonApi\Exceptions\JsonApiException(new \Neomerx\JsonApi\Document\Error("string-idx"));
        //$user = $GLOBALS['user']->getAuthenticatedUser();
        $users = \User::findBySql('1');

        return $this->getContentResponse($users);
    }
}
