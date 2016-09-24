<?php

namespace Argonauts\Contracts;

/*
 * JSONAPI Plugins add routes to the JSONAPI
 */

interface JsonApiPlugin
{
    public function registerAuthorizedRoutes(\Slim\App $app);
    public function registerUnauthorizedRoutes(\Slim\App $app);
}
