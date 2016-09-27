<?php

namespace Argonauts\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Argonauts\JsonApiController;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Document\Error;

class UserUpdate extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$user = \User::findByUsername($args['id'])) {
            throw new JsonApiException(new Error("User could not be found"));
        }

        $document = $this->getDocument();
        $user->Vorname = $document['data']['attributes']['first_name'];
        $user->Nachname = $document['data']['attributes']['last_name'];

        return $this->getContentResponse($user);
    }
}
