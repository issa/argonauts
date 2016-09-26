<?php

namespace Argonauts;

use Argonauts\Contracts\JsonApiPlugin;
use Argonauts\Middlewares\Authorization;
use Argonauts\Middlewares\JsonApi as JsonApiMiddleware;
use Argonauts\Routes\AuthorizedExample;
use Argonauts\Routes\UnauthorizedExample;
use Argonauts\Routes\UsersIndex;

class JsonApi
{
    public function __construct(\Slim\App $app, \StudipPlugin $plugin)
    {
        $this->app = $app;
        $this->plugin = $plugin;
    }

    public function __invoke()
    {
        $this->app->add(new JsonApiMiddleware($this->app));

        $this->app->group('', [$this, 'authorizedRoutes'])->add(new Authorization($this->app, $this->plugin));
        $this->app->group('', [$this, 'unauthorizedRoutes']);
    }

    public function authorizedRoutes()
    {
        \PluginEngine::sendMessage(JsonApiPlugin::class, 'registerAuthorizedRoutes', $this->app);

        $this->app->get('/auth', AuthorizedExample::class);
        $this->app->get('/users', UsersIndex::class);
    }

    public function unauthorizedRoutes()
    {
        \PluginEngine::sendMessage(JsonApiPlugin::class, 'registerUnauthorizedRoutes', $this->app);

        $this->app->get('/unauth', UnauthorizedExample::class);
    }
}
