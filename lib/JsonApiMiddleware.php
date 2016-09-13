<?php
namespace Argonauts;

use \Neomerx\JsonApi\Document\Error;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;

class JsonApiMiddleware {

    public function __construct($app, $plugin)
    {
        $this->app = $app;
        $this->plugin = $plugin;
    }

    public function __invoke($req, $res, $next)
    {
        $c = $this->app->getContainer();

        /*
        $oldErrorHandler = $this->replaceErrorHandler($c, function ($c) {
            return new ErrorHandler($c);
        });

        $this->replaceErrorHandler($c, $oldErrorHandler);
        */

        return $next($req, $res);
    }

    private function replaceErrorHandler($c, $errorHandler)
    {
        $old = $c['errorHandler'];
        unset($c['errorHandler']);
        $c['errorHandler'] = $errorHandler;
        return $old;
    }
}
