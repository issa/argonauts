<?php

namespace Argonauts\JsonApiIntegration;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Http\Responses as NeomerxResponses;
use Slim\Http\Headers;
use Slim\Http\Response;

/*
 * @author info@neomerx.com (www.neomerx.com)
 * @author mlunzena@uos.de
 */
class Responses extends NeomerxResponses
{
    /**
     * @var EncodingParametersInterface|null
     */
    private $parameters;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var MediaTypeInterface
     */
    private $outputMediaType;

    /**
     * @var SupportedExtensionsInterface
     */
    private $extensions;

    /**
     * @var ContainerInterface
     */
    private $schemes;

    /**
     * @var null|string
     */
    private $urlPrefix;

    /**
     * @param MediaTypeInterface               $outputMediaType
     * @param SupportedExtensionsInterface     $extensions
     * @param EncoderInterface                 $encoder
     * @param ContainerInterface               $schemes
     * @param EncodingParametersInterface|null $parameters
     * @param string|null                      $urlPrefix
     */
    public function __construct(
        MediaTypeInterface $outputMediaType,
        SupportedExtensionsInterface $extensions,
        EncoderInterface $encoder,
        ContainerInterface $schemes,
        EncodingParametersInterface $parameters = null,
        $urlPrefix = null
    ) {
        $this->extensions = $extensions;
        $this->encoder = $encoder;
        $this->outputMediaType = $outputMediaType;
        $this->schemes = $schemes;
        $this->urlPrefix = $urlPrefix;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function createResponse($content, $statusCode, array $headers)
    {
        $headers = new Headers($headers);
        $response = new Response($statusCode, $headers);
        $response->getBody()->write($content);

        return $response->withProtocolVersion('1.1');
    }

    /**
     * {@inheritdoc}
     */
    protected function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEncodingParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaContainer()
    {
        return $this->schemes;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedExtensions()
    {
        return $this->extensions;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMediaType()
    {
        return $this->outputMediaType;
    }
}
