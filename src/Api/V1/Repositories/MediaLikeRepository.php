<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class MediaLikeRepository implements RepositoryInterface
{
    protected MediaLike $model;

    public function __construct($model)
    {
        if (! ($model instanceof MediaLike)) {
            throw new \InvalidArgumentException('$model should be of MediaLike type');
        }

        $this->model = $model;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Create MediaLike.
     *
     * @param array $data
     * @return MediaLike|null
     */
    public function create(array $data)
    {
        $creator = auth()->user();

        try {

            return MediaLike::create([
                'media_id' => $data['media_id'],
                'user_id' => $data['user_id'],
                'creator' => ['_id' => new ObjectId($creator->_id)],
            ]);

        } catch (\Exception $e) {
            
            Log::error($e->getMessage());

            return null;
        }
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * Check if already liked the media.
     *
     * @param Media $media
     * @return MediaLike|void
     */
    public function isLiked(Media $media)
    {
        $creator = auth()->user();

        return MediaLike::media($media->_id)->creator($creator->_id)->first();
    }
}
