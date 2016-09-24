<?php

namespace Argonauts;

use Slim\App;
use StudipPlugin;
use Argonauts\Middlewares\RemoveTrailingSlashes;

class AppFactory
{
    public function makeApp(StudipPlugin $plugin)
    {
        $app = new App();
        $app = $this->configureContainer($app, $plugin);
        $app->add(new RemoveTrailingSlashes());

        return $app;
    }

    private function configureContainer($app, $plugin)
    {
        $container = $app->getContainer();
        $container['plugin'] = $plugin;
        $container['settings']['displayErrorDetails'] = true;

        return $app;
    }
}
