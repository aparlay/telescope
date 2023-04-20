<?php

namespace Aparlay\Core\Microservices\ws\media;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Microservices\ws\WsEventDispatcher;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Exception;
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

        $this->mediaId  = !empty($this->mediaId) ? new ObjectId($this->mediaId) : null;
        $this->userId   = !empty($this->userId) ? new ObjectId($this->userId) : null;
        $this->deviceId = !empty($this->deviceId) ? (string) $this->deviceId : null;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Exception
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
        if ($this->durationWatched > 3 && ($media = Media::media($this->mediaId)->first()) !== null) {
            if ($this->durationWatched <= $media->length) {
                $media->length_watched += $this->durationWatched;
            }
            $media->visit_count++;
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['visits' => DT::utcNow()]
            );
            $media->save();
        }
    }

    /**
     * @throws Exception
     */
    private function userVisit(): void
    {
        if (($model = MediaVisit::query()->user($this->userId)->dateString(date('Y-m-d'))->first()) === null) {
            $model          = new MediaVisit();
            $model->date    = date('Y-m-d');
            $model->user_id = $this->userId;
        }

        $model->media_id = $this->mediaId;
        $model->duration = $this->durationWatched;

        $media           = $model->mediaObj;
        if ($model->duration > ($media->length / 4)) {
            if ($model->duration <= $media->length) {
                $media->length_watched += $model->duration;
            }
            $media->visit_count++;
            $media->addToSet('visits', [
                '_id' => new ObjectId($model->userObj->_id),
                'username' => $model->userObj->username,
                'avatar' => $model->userObj->avatar,
            ], 10);
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['visits' => DT::utcNow()]
            );
            $media->save();
        }

        if (!$model->save()) {
            throw new Exception('Cannot save data.');
        }
    }
}
