<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class MediaLikeRepository
{
    protected MediaLike $model;

    public function __construct($model)
    {
        if (! ($model instanceof MediaLike)) {
            throw new \InvalidArgumentException('$model should be of MediaLike type');
        }

        $this->model = $model;
    }

    /**
     * Create MediaLike.
     *
     * @param array $data
     * @return MediaLike|null
     */
    public function create(array $data)
    {
        try {
            return MediaLike::create([
                'media_id' => $data['media_id'],
                'user_id' => $data['user_id'],
                'creator' => $data['creator'],
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * Delete MediaLike.
     *
     * @param string $id
     * @return void
     */
    public function delete($id)
    {
        $this->model->destroy($id);
    }

    /**
     * Check if already liked the media.
     *
     * @param  User|Authenticatable  $creator
     * @param  Media  $media
     * @return MediaLike|null
     */
    public function isLiked(User|Authenticatable $creator, Media $media): ?MediaLike
    {
        return MediaLike::media($media->_id)->creator($creator->_id)->first();
    }
}
