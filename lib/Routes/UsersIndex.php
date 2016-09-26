<?php

namespace Argonauts\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;

class UsersIndex extends JsonApiController
{
    protected $allowedIncludePaths = ['contacts'];

    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        //throw new \RuntimeException();
        //throw new \Neomerx\JsonApi\Exceptions\JsonApiException(new \Neomerx\JsonApi\Document\Error("string-idx"));
        //$user = $GLOBALS['user']->getAuthenticatedUser();

        $users = \User::findBySql('1');
        $total = count($users);
        extract($this->getOffsetAndLimit());
        $slice = array_slice($users, $offset, $limit);

        return $this->getPaginatedContentResponse($slice, $total);
    }
}
