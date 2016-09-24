<?php

namespace Argonauts\Providers;

use Argonauts\JsonApiIntegration\Config as C;
use Argonauts\JsonApiIntegration\Errors\ExceptionThrower;
use Argonauts\JsonApiIntegration\Factories\Factory;
use Argonauts\JsonApiIntegration\Http\Responses;
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

class JsonApiServices implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $container)
    {
        // register factory
        $container[FactoryInterface::class] = function ($c) {
            $factory = new Factory();

            if ($c->has('logger')) {
                $factory->setLogger($c['logger']);
            }

            return $factory;
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
     * @param ContainerInterface $container
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
        $urlPrefix = $container[C::NAME][C::JSON_URL_PREFIX];

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

        $urlPrefix = isset($config[C::JSON_URL_PREFIX]) === true
                   ? $config[C::JSON_URL_PREFIX]
                   : null;
        $encoderOptions = new EncoderOptions(0, $urlPrefix);

        $decoderClosure = $this->getDecoderClosure();
        $encoderClosure = $this->getEncoderClosure($factory, $schemaContainer, $encoderOptions, $config);
        $codecMatcher = $factory->createCodecMatcher();
        $jsonApiType = $factory->createMediaType(
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
            return $factory->createEncoder($container, $encoderOptions);
        };
    }
}
