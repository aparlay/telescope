<?php

namespace Aparlay\Core\Facades;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTProvider;

class RestartJwtFacades
{
    public function handle($event): void
    {
        JWTAuth::clearResolvedInstances();
        JWTFactory::clearResolvedInstances();
        JWTProvider::clearResolvedInstances();
    }
}
