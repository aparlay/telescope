<?php

namespace Aparlay\Core\Microservices\ws\media;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Microservices\ws\WsEventDispatcher;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Exception;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use MongoDB\BSON\ObjectId;

class Watch implements WsEventDispatcher
{
    public $userId;
    public $anonymousId;
    public $deviceId;
    public $mediaId;
    public $durationWatched;
    public $durationTotal;

    public function __construct($config = [])
    {
        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        if (empty($this->mediaId)) {
            throw new InvalidArgumentException('mediaId is mandatory field for the "media.watch" event.');
        }

        if (empty($this->userId) && empty($this->anonymousId)) {
            throw new InvalidArgumentException('one of the userId or anonymousId is mandatory field for the "media.watch" event.');
        }

        $this->mediaId = new ObjectId($this->mediaId);
        $this->userId = ! empty($this->userId) ? new ObjectId($this->userId) : null;
        $this->deviceId = ! empty($this->deviceId) ? (string) $this->deviceId : null;
    }

    /**
     * @throws Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function execute()
    {
        if (empty($this->mediaId)) {
            return;
        }

        if (empty($this->userId)) {
            $this->anonymousVisit();
        } else {
            $this->userVisit();
        }
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException|Exception
     */
    private function anonymousVisit()
    {
        /** @var Media $media */
        if ($this->durationWatched > 1 && ($media = Media::find($this->mediaId)) !== null) {
            if ($this->durationWatched <= $media->length) {
                $media->length_watched += $this->durationWatched;
            }
            $media->visit_count++;
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['visits' => DT::utcNow()]
            );
            $media->save();

            $cacheKey = (new MediaVisit())->getCollection().$this->deviceId;
            $visited = [];
            if (Cache::store('redis')->has($cacheKey)) {
                $visited = Cache::store('redis')->get($cacheKey);
            }

            $visited[] = $this->mediaId;

            Cache::store('redis')->set($cacheKey, array_unique($visited, SORT_REGULAR), config('app.cache.veryLongDuration'));
        }
    }

    /**
     * @throws Exception
     */
    private function userVisit(): void
    {
        if (($model = MediaVisit::user($this->userId)->date(date('Y-m-d'))->first()) === null) {
            $model = new MediaVisit();
            $model->user_id = $this->userId;
        }

        $model->media_id = $this->mediaId;
        $model->duration = $this->durationWatched;

        if (! $model->save()) {
            throw new Exception('Cannot save data.');
        }
    }
}
