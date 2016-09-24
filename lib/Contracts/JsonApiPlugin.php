<?php

namespace Argonauts\Contracts;

/*
 * JSONAPI Plugins add routes to the JSONAPI
 */

interface JsonApiPlugin
{
    public function registerRoutes(\Slim\App $app);
}
