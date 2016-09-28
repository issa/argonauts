<?php

namespace Argonauts\JsonApiIntegration;

use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersCheckerInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersInterface;

/*
 * @author info@neomerx.com (www.neomerx.com)
 * @author mlunzena@uos.de
 */
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

    /**
     * @var Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var ParametersCheckerInterface
     */
    private $parametersChecker;

    /**
     * @var bool
     */
    private $parametersChecked = false;

    /**
     * Init integrations with JSON API implementation.
     *
     * @param $container
     */
    private function initJsonApiSupport($container)
    {
        $this->container = $container;

        $factory = $container[FactoryInterface::class];

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
        $this->checkQueryParameters();
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
    protected function getMetaResponse($meta, $statusCode = ResponsesInterface::HTTP_OK, $headers = [])
    {
        $this->checkQueryParameters();
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
        $meta = null,
        array $headers = []
    ) {
        $this->checkQueryParameters();
        $responses = $this->container[ResponsesInterface::class];

        return $responses->getContentResponse($data, $statusCode, $links, $meta, $headers);
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
        $this->checkQueryParameters();
        $responses = $this->container[ResponsesInterface::class];

        return $responses->getCreatedResponse($resource, $links, $meta, $headers);
    }

    protected function getPaginatedContentResponse(
        $data,
        $total,
        $statusCode = ResponsesInterface::HTTP_OK,
        $links = null,
        $meta = null,
        array $headers = []
    ) {
        $this->checkQueryParameters();
        $responses = $this->container[ResponsesInterface::class];

        extract($this->getOffsetAndLimit());

        if (!$meta) {
            $meta = [];
        }

        $meta['page'] = [
            'offset' => (int) $offset,
            'limit' => (int) $limit,
            'total' => (int) $total,
        ];

        $paginator = new Paginator($data, $total, $offset, $limit);

        if (!$links) {
            $links = [];
        }

        foreach (words('first last prev next') as $rel) {
            if (list($off, $lim) = $paginator->{'get'.ucfirst($rel).'PageOffsetAndLimit'}()) {
                $links[$rel] = $this->createLink($off, $lim);
            }
        }

        return $responses->getContentResponse($data, $statusCode, $links, $meta, $headers);
    }

    /**
     * @return mixed
     */
    protected function getDocument()
    {
        $request = $this->container['request'];
        $codecMatcher = $this->container[CodecMatcherInterface::class];
        if ($codecMatcher->getDecoder() === null) {
            $parameters = $this->container[HeaderParametersInterface::class];
            $codecMatcher->matchDecoder($parameters->getContentTypeHeader());
        }
        $decoder = $codecMatcher->getDecoder();

        return $decoder->decode($request->getBody()->getContents());
    }

    protected function checkQueryParameters()
    {
        $this->parametersChecker->checkQuery($this->container[EncodingParametersInterface::class]);
        $this->parametersChecked = true;
    }

    /**
     * @return ParametersInterface
     */
    protected function getQueryParameters()
    {
        if ($this->parametersChecked === false) {
            $this->checkQueryParameters();
        }

        return $this->container[EncodingParametersInterface::class];
    }

    protected function getOffsetAndLimit($offsetDefault = 0, $limitDefault = 30)
    {
        $params = $this->getQueryParameters()->getPaginationParameters();

        return [
            'offset' => $params && array_key_exists('offset', $params) ? (int) $params['offset'] : $offsetDefault,
            'limit' => $params && array_key_exists('limit', $params) ? (int) $params['limit'] : $limitDefault,
        ];
    }

    private function createLink($offset, $limit)
    {
        $factory = $this->container[FactoryInterface::class];
        $request = $this->container['request'];

        $queryParams = $request->getQueryParams();
        $queryParams['page']['offset'] = $offset;
        $queryParams['page']['limit'] = $limit;

        $uri = $request->getUri()->withQuery(http_build_query($queryParams));

        return $factory->createLink((string) $uri, null, true);
    }
}
