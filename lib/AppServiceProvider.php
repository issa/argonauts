<?php

namespace Argonauts;

use \Neomerx\Limoncello\Http\AppServiceProviderTrait;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;

class AppServiceProvider
{
    use AppServiceProviderTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register($slimApp)
    {
        $integration = new SlimIntegration($slimApp->getContainer());

        $this->registerResponses($integration);
        $this->registerCodecMatcher($integration);
        $this->registerExceptionThrower($integration);

        $integration->setInContainer(IntegrationInterface::class, $integration);
    }
}
