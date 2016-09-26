<?php

namespace Argonauts;

use Argonauts\JsonApiIntegration\JsonApiTrait;

/*
use Neomerx\JsonApi\Contracts\Http\Headers\HeadersCheckerInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersInterface;
*/

class JsonApiController
{
    use JsonApiTrait;

    public function __construct($container)
    {
        $this->container = $container;
        $this->initJsonApiSupport($container);
        /*
        $headerChecker = $this->container[HeadersCheckerInterface::class];
        $headerChecker->checkHeaders($this->container[HeaderParametersInterface::class]);
        */
    }
}
