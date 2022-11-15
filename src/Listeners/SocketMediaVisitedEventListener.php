<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Models\MediaVisit;
use Aparlay\Core\Jobs\MediaWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;

class SocketMediaVisitedEventListener implements ShouldQueue
{
    public function handle($event)
    {
        if ($event->payload['name'] !== 'client_event' || $event->payload['event'] !== 'client_media_visited') {
            return;
        }

        $payload = $event->payload['data'];
        if (isset($payload['user_id'], $payload['media_id'], $payload['device_id'])) {
            MediaWatched::dispatch([new ObjectId($payload['media_id'])], (int)$payload['duration'], $payload['user_id']);

            $cacheKey = (new MediaVisit())->getCollection().':'.$payload['device_id'];
            $visited = Cache::store('redis')->get($cacheKey, []);
            $visited[] = $payload['media_id'];
            Cache::store('redis')->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));
        }
    }
}
