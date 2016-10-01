<?php

namespace Argonauts\JsonApiIntegration;

use Neomerx\JsonApi\Factories\Factory as NeomerxFactory;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parser\ParserManagerInterface;

/**
 * Die "normale" \Neomerx\JsonApi\Factories\Factory stellt in
 * Factory::createParser einen \Neomerx\JsonApi\Encoder\Parser\Parser
 * her. Dieser hat aber Probleme mit Instanzen von \SimpleORMap,
 * sodass diese Factory einen speziellen EncoderParser herstellt, der
 * diese Probleme nicht hat.
 *
 * @see \Neomerx\JsonApi\Factories\Factory
 * @see \Neomerx\JsonApi\Encoder\Parser\Parser
 * @see EncoderParser
 */
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
