<?php

namespace Argonauts\JsonApiIntegration;

/*
 * Copyright 2015 info@neomerx.com (www.neomerx.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersCheckerInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

trait JsonApiTrait
{
    /**
     * If unrecognized parameters should be allowed in input parameters.
     *
     * @var bool
     */
    protected $allowUnrecognizedParams = false;

    /**
     * A list of allowed include paths in input parameters.
     *
     * Empty array [] means clients are not allowed to specify include paths and 'null' means all paths are allowed.
     *
     * @var string[]|null
     */
    protected $allowedIncludePaths = [];

    /**
     * A list of JSON API types which clients can sent field sets to.
     *
     * Possible values
     *
     * $allowedFieldSetTypes = null; // <-- for all types all fields are allowed
     *
     * $allowedFieldSetTypes = []; // <-- non of the types and fields are allowed
     *
     * $allowedFieldSetTypes = [
     *      'people'   => null,              // <-- all fields for 'people' are allowed
     *      'comments' => [],                // <-- no fields for 'comments' are allowed (all denied)
     *      'posts'    => ['title', 'body'], // <-- only 'title' and 'body' fields are allowed for 'posts'
     * ];
     *
     * @var array|null
     */
    protected $allowedFieldSetTypes = null;

    /**
     * A list of allowed sort field names in input parameters.
     *
     * Empty array [] means clients are not allowed to specify sort fields and 'null' means all fields are allowed.
     *
     * @var string[]|null
     */
    protected $allowedSortFields = [];

    /**
     * A list of allowed pagination input parameters (e.g 'number', 'size', 'offset' and etc).
     *
     * Empty array [] means clients are not allowed to specify paging and 'null' means all parameters are allowed.
     *
     * @var string[]|null
     */
    protected $allowedPagingParameters = [];

    /**
     * A list of allowed filtering input parameters.
     *
     * Empty array [] means clients are not allowed to specify filtering and 'null' means all parameters are allowed.
     *
     * @var string[]|null
     */
    protected $allowedFilteringParameters = [];

    private $container;

    /**
     * @var CodecMatcherInterface
     */
    private $codecMatcher;

    /**
     * @var ParametersCheckerInterface
     */
    private $parametersChecker;

    /**
     * @var ParametersInterface
     */
    private $parameters = null;

    /**
     * @var bool
     */
    private $parametersChecked = false;

    /**
     * Init integrations with JSON API implementation.
     *
     * @param
     */
    private function initJsonApiSupport($container)
    {
        $this->container = $container;

        $factory = $container[FactoryInterface::class];

        $this->codecMatcher = $container[CodecMatcherInterface::class];

        $this->parametersChecker = $factory->createQueryChecker(
            $this->allowUnrecognizedParams,
            $this->allowedIncludePaths,
            $this->allowedFieldSetTypes,
            $this->allowedSortFields,
            $this->allowedPagingParameters,
            $this->allowedFilteringParameters
        );
    }

    // ***** RESPONSE GENERATORS *****

    /**
     * Get response with HTTP code only.
     *
     * @param $statusCode
     *
     * @return Response
     */
    protected function getCodeResponse($statusCode, array $headers = [])
    {
        $this->checkParameters();

        $responses = $this->container[ResponsesInterface::class];

        return $responses->getCodeResponse($statusCode, $headers);
    }

    /**
     * Get response with meta information only.
     *
     * @param array|object $meta       Meta information
     * @param int          $statusCode
     *
     * @return Response
     */
    protected function getMetaResponse($meta, $statusCode = Response::HTTP_OK, $headers = [])
    {
        $this->checkParameters();

        $responses = $this->container[ResponsesInterface::class];

        return $responses->getMetaResponse($meta, $statusCode, $headers);
    }

    /**
     * Get response with regular JSON API Document in body.
     *
     * @param object|array                                                       $data
     * @param int                                                                $statusCode
     * @param array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface>|null $links
     * @param mixed                                                              $meta
     *
     * @return Response
     */
    protected function getContentResponse(
        $data,
        $statusCode = ResponsesInterface::HTTP_OK,
        $links = null,
        $meta = null
    ) {
        $this->checkParameters();
        $responses = $this->container[ResponsesInterface::class];

        return $responses->getContentResponse($data, $statusCode, $links, $meta);
    }

    /**
     * @param object                                                             $resource
     * @param array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface>|null $links
     * @param mixed                                                              $meta
     *
     * @return Response
     */
    protected function getCreatedResponse(
        $resource,
        $links = null,
        $meta = null,
        array $headers = []
    ) {
        $this->checkParameters();
        $responses = $this->container[ResponsesInterface::class];

        return $responses->getCreatedResponse($resource, $links, $meta, $headers);
    }

    /**
     * @return mixed
     */
    protected function getDocument()
    {
        if ($this->codecMatcher->getDecoder() === null) {
            $this->codecMatcher->findDecoder($this->getParameters()->getContentTypeHeader());
        }

        $decoder = $this->codecMatcher->getDecoder();

        return $decoder->decode($this->container['request']->getBody());
    }

    protected function checkParameters()
    {
        $this->parametersChecker->checkQuery($this->container[EncodingParametersInterface::class]);
        $this->parametersChecked = true;
    }

    /**
     * @return ParametersInterface
     */
    protected function getParameters()
    {
        if ($this->parametersChecked === false) {
            $this->checkParameters();
        }

        return $this->container[EncodingParametersInterface::class];
    }
}
