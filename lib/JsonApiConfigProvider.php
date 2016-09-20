<?php

namespace Argonauts;

use Argonauts\JsonApiIntegration\Config\Config as C;

class JsonApiConfigProvider implements \Pimple\ServiceProviderInterface
{
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function register(\Pimple\Container $container)
    {
        $container[C::NAME] = [
            C::SCHEMAS => [
                \User::class => \Argonauts\Schema\User::class,
            ],
            C::JSON => [
                C::JSON_URL_PREFIX => rtrim(\PluginEngine::getURL($this->plugin, [], ''), '/'),
            ],
        ];
    }
}
