<?php

namespace Argonauts;

use \Neomerx\Limoncello\Http\JsonApiTrait;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;

class JsonApiController
{
    use JsonApiTrait;

    public function __construct($container)
    {
        $this->container = $container;
        $integration = $container[IntegrationInterface::class];
        $this->initJsonApiSupport($integration);
        $this->checkParameters();
    }
}
