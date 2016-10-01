<?php

namespace Argonauts\Errors;

use Interop\Container\ContainerInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Document\Error;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Dieser spezielle Exception Handler wird in der Slim-Applikation
 * für alle JSON-API-Routen installiert und sorgt dafür, dass auch
 * evtl. Fehler JSON-API-kompatibel geliefert werden.
 */
class JsonApiExceptionHandler
{
    private $previous;

    private $container;

    /**
     * Der Konstruktor...
     *
     * @param ContainerInterface $container der Dependency Container,
     *                                      der in der Slim-Applikation verwendet wird
     * @param callable           $previous  der zuvor installierte `Error
     *                                      Handler` als Fallback
     */
    public function __construct(ContainerInterface $container, $previous = null)
    {
        $this->previous = $previous;
        $this->container = $container;
    }

    /**
     * Diese Methode wird aufgerufen, sobald es zu einer Exception
     * kam, und generiert eine entsprechende JSON-API-spezifische Response.
     *
     * @param Request    $request   der eingehende Request
     * @param Response   $response  die vorbereitete ausgehende Response
     * @param \Exception $exception die aufgetretene Exception
     *
     * @return Response die JSON-API-kompatible Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        if ($exception instanceof JsonApiException) {
            return $this->createJsonApiResponse($exception);
        } else {
            $httpCode = 500;
            $details = null;

            $debugEnabled = \Studip\ENV === 'development';
            if ($debugEnabled === true) {
                $message = $exception->getMessage();
                $details = (string) $exception;
            }
            $errors = new ErrorCollection();
            $errors->add(new Error(null, null, $httpCode, null, $message, $details));

            return $this->createErrorResponse($errors, $httpCode);
        }

        return $this->previous === null
                               ? null
                               : call_user_func_array(
                                   $this->previous,
                                   [$request, $response, $exception]
                               );
    }

    private function createJsonApiResponse(JsonApiException $exception)
    {
        $errors = $exception->getErrors();
        $code = $exception->getHttpCode();

        return $this->createErrorResponse($errors, $code);
    }

    private function createErrorResponse($errors, $code)
    {
        $responses = $this->container[ResponsesInterface::class];

        switch (count($errors)) {
        case 0:
            $response = $responses->getCodeResponse($code);
            break;

        case 1:
            $response = $responses->getErrorResponse($errors[0], $code);
            break;

        default:
            $response = $responses->getErrorResponse($errors, $code);
            break;
        }

        return $response;
    }
}
