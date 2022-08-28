<?php

namespace Aparlay\Core\Facades;

use JKocik\Laravel\Profiler\Events\ResetTrackers;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTProvider;

class RestartIncompatibleFacades
{
    public function handle($event): void
    {
        JWTAuth::clearResolvedInstances();
        JWTFactory::clearResolvedInstances();
        JWTProvider::clearResolvedInstances();
        event(ResetTrackers::class);
    }
}
