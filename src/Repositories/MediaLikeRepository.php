<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
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

    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Create MediaLike.
     *
     * @param array $data
     * @return MediaLike
     */
    public function create(array $data)
    {
        $creator = auth()->user();
        $modal = new MediaLike(
            array_merge($data, [
                'creator' => ['_id' => new ObjectId($creator->_id)],
            ])
        );
        $modal->save();

        return $modal;
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
