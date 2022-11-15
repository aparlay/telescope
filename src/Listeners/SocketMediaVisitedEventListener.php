<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Jobs\MediaWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use MongoDB\BSON\ObjectId;

class SocketMediaVisitedEventListener implements ShouldQueue
{
    public function handle($event)
    {
        if ($event->payload['name'] !== 'client_event' || $event->payload['event'] !== 'client-media-visited') {
            return;
        }

        $payload = $event->payload['data'];
        if (isset($payload['user_id'], $payload['media_id'], $payload['device_id'])) {
            MediaWatched::dispatch(new ObjectId($payload['media_id']), $payload['duration'], $payload['user_id']);
        }
    }
}
