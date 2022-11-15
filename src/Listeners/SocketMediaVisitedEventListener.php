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

        $data = $event->payload['data'];
        if (isset($data['user_id'], $data['media_id'], $data['device_id'], $data['duration'])) {
            MediaWatched::dispatch(new ObjectId($data['media_id']), $data['duration'], $data['user_id']);
        }
    }
}
