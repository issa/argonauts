<?php

namespace Argonauts\JsonApiIntegration;

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
        $parser = new EncoderParser($this, $this, $this, $container, $manager);
        $parser->setLogger($this->logger);

        return $parser;
    }
}
