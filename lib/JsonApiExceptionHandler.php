<?php

namespace Argonauts;

use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Document\Error;

class JsonApiExceptionHandler
{
    private $previous;

    private $container;

    public function __construct($container, $previous = null)
    {
        $this->previous = $previous;
        $this->container = $container;
    }

    public function __invoke($request, $response, $exception)
    {
        if ($exception instanceof JsonApiException) {
            return $this->createJsonApiResponse($exception);
        }
        /*
          else {
            $httpCode = 500;
            $details = null;

            #$debugEnabled = $container->get(C::class)
            #    ->getConfigValue(C::KEY_APP, C::KEY_APP_DEBUG_MODE);
            $debugEnabled = true;

            if ($debugEnabled === true) {
                $message = $exception->getMessage();
                $details = (string)$exception;
            }
            $errors = new ErrorCollection();
            $errors->add(new Error(null, null, $httpCode, null, $message, $details));

            return $this->createErrorResponse($errors, $httpCode);
          }
        */

        return $this->previous === null ? null : call_user_func_array($this->previous, [$request, $response, $exception]);
    }

    /**
     * @param JsonApiException $exception
     *
     * @return Response
     */
    protected function createJsonApiResponse(JsonApiException $exception)
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
