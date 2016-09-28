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
        extract($this->getOffsetAndLimit());
        $total = \User::countBySql();

        $users = \User::findBySql(
            '1 ORDER BY username LIMIT ? OFFSET ?',
            [$limit, $offset]
        );

        return $this->getPaginatedContentResponse($users, $total);
    }
}
