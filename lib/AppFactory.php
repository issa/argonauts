<?php

namespace Argonauts;

use \Slim\App;
use \StudipPlugin;
use \Neomerx\Limoncello\Config\Config as C;

class AppFactory {

    public function makeApp(StudipPlugin $plugin)
    {
        $app = new App();
        $app = $this->configureContainer($app, $plugin);
        $app->add(new RemoveTrailingSlashesMiddleware());
        return $app;
    }

    private function configureContainer($app, $plugin)
    {
        $container = $app->getContainer();
        $container['plugin'] = $plugin;
        $container['settings']['displayErrorDetails'] = true;


        $container['limoncello'] = [
            C::SCHEMAS => [
                \User::class => \Argonauts\Schema\User::class
            ],
            C::JSON => [
                C::JSON_URL_PREFIX => rtrim(\PluginEngine::getURL($plugin, [], ''), '/')
            ]
        ];

        $asp = new \Argonauts\AppServiceProvider();
        $asp->register($app);


        return $app;
    }
}
