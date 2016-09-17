<?php

namespace Argonauts\JsonApiIntegration\Factories;

use Argonauts\JsonApiIntegration\Encoder\Parser\Parser;
use Neomerx\JsonApi\Factories\Factory as NeomerxFactory;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parser\ParserManagerInterface;

class Factory extends NeomerxFactory
{
    /**
     * {@inheritdoc}
     */
    public function createParser(ContainerInterface $container, ParserManagerInterface $manager)
    {
        $parser = new Parser($this, $this, $this, $container, $manager);
        $parser->setLogger($this->logger);

        return $parser;
    }
}
