<?php

namespace Argonauts;

use Argonauts\Limoncello\Http\JsonApiTrait;

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
