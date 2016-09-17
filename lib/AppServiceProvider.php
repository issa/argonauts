<?php

namespace Argonauts;

use Argonauts\Limoncello\Config\Config as C;
use Argonauts\Limoncello\Errors\ExceptionThrower;
use Argonauts\Limoncello\Factories\Factory;
use Argonauts\Limoncello\Http\Responses;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeadersCheckerInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Contracts\Integration\ExceptionThrowerInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Neomerx\JsonApi\Http\Headers\SupportedExtensions;

class AppServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $container)
    {
        // register factory
        $container[FactoryInterface::class] = function ($c) {
            return new Factory();
        };

        // register config
        if (!isset($container[C::NAME])) {
            $container[C::NAME] = [];
        }
        $config = $container[C::NAME];
        $container[C::class] = $config; // TODO???

        // register schemas
        $container[ContainerInterface::class] = function ($c) {
            $schemas = isset($c[C::NAME][C::SCHEMAS]) ? $c[C::NAME][C::SCHEMAS] : [];

            return $c[FactoryInterface::class]->createContainer($schemas);
        };

        // register codec matcher
        $container[CodecMatcherInterface::class] = function ($c) {
            return $this->createCodecMatcher($c);
        };

        // TODO wo wird das gebraucht
        $container[HeadersCheckerInterface::class] = function ($c) {
            return $c[FactoryInterface::class]->createHeadersChecker($c[CodecMatcherInterface::class]);
        };

        // register query params
        $container[EncodingParametersInterface::class] = function ($c) {
            return $c[FactoryInterface::class]->createQueryParametersParser()->parse($c['request']);
        };

        $container[HeaderParametersInterface::class] = function ($c) {
            return $c[FactoryInterface::class]->createHeaderParametersParser()->parse($c['request']);
        };

        // register responses
        $container[ResponsesInterface::class] = function ($c) {
            return $this->createResponses($c);
        };

        $container[ExceptionThrowerInterface::class] = function ($c) {
            return new ExceptionThrower();
        };
    }

    /**
     * @param ContainerInterface               $container
     *
     * @return ResponsesInterface
     */
    protected function createResponses($container)
    {
        $codecMatcher = $container[CodecMatcherInterface::class];
        $parameters = $container[EncodingParametersInterface::class];
        $params = $container[HeaderParametersInterface::class];

        $codecMatcher->matchEncoder($params->getAcceptHeader());
        $encoder = $codecMatcher->getEncoder();

        $schemaContainer = $container[ContainerInterface::class];
        $urlPrefix = $container[C::NAME][C::JSON][C::JSON_URL_PREFIX];

        $responses = new Responses(
            new MediaType(MediaTypeInterface::JSON_API_TYPE, MediaTypeInterface::JSON_API_SUB_TYPE),
            new SupportedExtensions(),
            $encoder,
            $schemaContainer,
            $parameters,
            $urlPrefix
        );

        return $responses;
    }

    /**
     * @param array              $config
     * @param FactoryInterface   $factory
     * @param ContainerInterface $schemaContainer
     *
     * @return CodecMatcherInterface
     */
    protected function createCodecMatcher($container)
    {
        $config = $container[C::class];
        $factory = $container[FactoryInterface::class];
        $schemaContainer = $container[ContainerInterface::class];

        $options = $this->getValue($config, C::JSON, C::JSON_OPTIONS, C::JSON_OPTIONS_DEFAULT);
        $urlPrefix = $this->getValue($config, C::JSON, C::JSON_URL_PREFIX, null);
        $depth = $this->getValue($config, C::JSON, C::JSON_DEPTH, C::JSON_DEPTH_DEFAULT);
        $encoderOptions = new EncoderOptions($options, $urlPrefix, $depth);

        $decoderClosure = $this->getDecoderClosure();
        $encoderClosure = $this->getEncoderClosure($factory, $schemaContainer, $encoderOptions, $config);
        $codecMatcher = $factory->createCodecMatcher();
        $jsonApiType = $factory->createMediaType(
#            'text',
#            'html'
            MediaTypeInterface::JSON_API_TYPE,
            MediaTypeInterface::JSON_API_SUB_TYPE
        );
        $jsonApiTypeUtf8 = $factory->createMediaType(
            MediaTypeInterface::JSON_API_TYPE,
            MediaTypeInterface::JSON_API_SUB_TYPE,
            ['charset' => 'UTF-8']
        );
        $codecMatcher->registerEncoder($jsonApiType, $encoderClosure);
        $codecMatcher->registerDecoder($jsonApiType, $decoderClosure);
        $codecMatcher->registerEncoder($jsonApiTypeUtf8, $encoderClosure);
        $codecMatcher->registerDecoder($jsonApiTypeUtf8, $decoderClosure);

        return $codecMatcher;
    }

    /**
     * @return Closure
     */
    protected function getDecoderClosure()
    {
        return function () {
            return new DocumentDecoder();
        };
    }

    /**
     * @param FactoryInterface   $factory
     * @param ContainerInterface $container
     * @param EncoderOptions     $encoderOptions
     * @param array              $config
     *
     * @return Closure
     */
    private function getEncoderClosure(
        FactoryInterface $factory,
        ContainerInterface $container,
        EncoderOptions $encoderOptions,
        array $config
    ) {
        return function () use ($factory, $container, $encoderOptions, $config) {
            $isShowVer = $this->getValue($config, C::JSON, C::JSON_IS_SHOW_VERSION, C::JSON_IS_SHOW_VERSION_DEFAULT);
            $versionMeta = $this->getValue($config, C::JSON, C::JSON_VERSION_META, null);
            $encoder = $factory->createEncoder($container, $encoderOptions);

            $isShowVer === false ?: $encoder->withJsonApiVersion($versionMeta);

            return $encoder;
        };
    }

    /**
     * @param array  $array
     * @param string $key1
     * @param string $key2
     * @param mixed  $default
     *
     * @return mixed
     */
    private function getValue(array $array, $key1, $key2, $default)
    {
        return isset($array[$key1][$key2]) === true ? $array[$key1][$key2] : $default;
    }
}
