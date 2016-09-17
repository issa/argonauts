<?php

namespace Argonauts\JsonApiIntegration\Encoder\Parser;

use SimpleORMap;
use Neomerx\JsonApi\Encoder\Parser\Parser as NeomerxParser;

class Parser extends NeomerxParser
{
    /**
     * {@inheritdoc}
     */
    protected function analyzeCurrentData()
    {
        $relationship = $this->stack->end()->getRelationship();
        $data = $relationship->isShowData() === true ? $relationship->getData() : null;

        if ($data instanceof SimpleORMap) {
            $isEmpty = false;
            $isCollection = false;
            $traversableData = [$data];

            return [$isEmpty, $isCollection, $traversableData];
        }

        return parent::analyzeCurrentData();
    }
}
