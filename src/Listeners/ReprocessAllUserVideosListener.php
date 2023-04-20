<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Events\AvatarChangedEvent;
use Aparlay\Core\Events\UsernameChangedEvent;
use Aparlay\Core\Jobs\ReprocessMedia;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReprocessAllUserVideosListener implements ShouldQueue
{
    public function handle(UsernameChangedEvent|AvatarChangedEvent $event)
    {
        $i      = 1;
        $medias = Media::creator($event->user->_id)
            ->whereIn('status', [
                MediaStatus::COMPLETED->value,
                MediaStatus::CONFIRMED->value,
                MediaStatus::DENIED->value, ])
            ->get();
        foreach ($medias as $media) {
            if (is_array($media->files_history) && !empty($media->files_history)) {
                $lastMediaFile = $media->files_history[array_key_last($media->files_history)];
                if (isset($lastMediaFile['file'])) {
                    ReprocessMedia::dispatch($media->_id, $lastMediaFile['file'])
                        ->delay(now()->addMinutes(2 * $i++))
                        ->onQueue('low');
                }
            }
        }
    }
}
