<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Admin\Services\MediaService;
use Aparlay\Core\Events\UserVisibilityChangedEvent;

class UpdateMediaVisibilityListener
{
    public function handle($event)
    {
        $mediaService = app()->make(MediaService::class);

        if ($event instanceof UserVisibilityChangedEvent) {
            $mediaService->updateVisibilityByCreator($event->creator, $event->visibility);
        }
    }
}
