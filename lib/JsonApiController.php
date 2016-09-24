<?php

namespace Argonauts;

use Argonauts\JsonApiIntegration\JsonApiTrait;

class JsonApiController
{
    use JsonApiTrait;

    public function __construct($container)
    {
        $this->container = $container;
        $this->initJsonApiSupport($container);
        $this->checkParameters();
    }
}
