<?php

namespace Aparlay\Core\Facades;

use JKocik\Laravel\Profiler\Events\ResetTrackers;

class RestartIncompatibleFacades
{
    public function handle($event): void
    {
        event(ResetTrackers::class);
    }
}
