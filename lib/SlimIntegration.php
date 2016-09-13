<?php

namespace Argonauts;

use \Slim\Http\Headers;
use \Slim\Http\Response;

use \Neomerx\Limoncello\Config\Config as C;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;

class SlimIntegration implements IntegrationInterface
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getConfig()
    {
        if (!isset($this->container[C::NAME])) {
            $this->container[C::NAME] = [];
        }

        #$config[C::JSON][C::JSON_URL_PREFIX] = \Request::getSchemeAndHttpHost();

        return $this->container[C::NAME];
    }

    public function getCurrentRequest()
    {
        return $this->getFromContainer('request');
    }

    public function getFromContainer($key)
    {
        return $this->container[$key];
    }

    public function setInContainer($key, $value)
    {
        $this->container[$key] = $value;
    }

    public function hasInContainer($key)
    {
        return isset($this->container[$key]);
    }

    public function createResponse($content, $statusCode, array $headers)
    {
        $headers = new Headers($headers);
        $response = new Response($statusCode, $headers);
        $response->getBody()->write($content);

        return $response->withProtocolVersion($this->container['settings']['httpVersion']);
    }

    public function getContent()
    {
        return $this->getCurrentRequest()->getBody();
    }

    public function getQueryParameters()
    {
        return $this->getCurrentRequest()->getQueryParams();
    }

    public function getHeader($name)
    {
        return $this->getCurrentRequest()->getHeaderLine($name);
    }
}
