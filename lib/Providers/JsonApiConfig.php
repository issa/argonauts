<?php

namespace Argonauts\Providers;

use Argonauts\JsonApiIntegration\Config as C;

class JsonApiConfig implements \Pimple\ServiceProviderInterface
{
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function register(\Pimple\Container $container)
    {
        $container[C::NAME] = [
            C::SCHEMAS => [
                \User::class => \Argonauts\Schemas\User::class,
                \Contact::class => \Argonauts\Schemas\Contact::class,
            ],
            C::JSON_URL_PREFIX => rtrim(\PluginEngine::getURL($this->plugin, [], ''), '/'),
        ];
    }
}
