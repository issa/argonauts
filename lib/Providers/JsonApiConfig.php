<?php

namespace Argonauts\Providers;

use Argonauts\JsonApiIntegration\Config as C;

/**
 * Diese Klasse konfiguriert die JSON-API in der Slim-Applikation um
 * die Zuordnung von Schemata zu Stud.IP-Model-Klassen und setzt das
 * URL-Prefix für die interne Generierung von URIs.
 */
class JsonApiConfig implements \Pimple\ServiceProviderInterface
{
    /**
     * Der Konstruktor benötigt das Stud.IP-Plugin.
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Diese Methode wird automatisch aufgerufen, wenn diese Klasse dem
     * Dependency Container der Slim-Applikation hinzugefügt wird.
     *
     * Hier werden die Schema-Abbildungen und das JSON-API-URL-Präfix gesetzt.
     *
     * @param \Pimple\Container $container der Dependency Container
     */
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
