<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class MediaLikeService
{
    use HasUserTrait;

    public function __construct()
    {
    }

    /**
     * Responsible to create like for given media.
     *
     * @param  Media  $media
     * @return array
     * @throws \Exception
     */
    public function create(Media $media)
    {
        $statusCode = Response::HTTP_OK;

        $creator = $this->getUser();
        if (($like = MediaLike::query()->media($media->_id)->creator($creator->_id)->first()) === null) {
            $like = MediaLike::create([
                'media_id' => new ObjectId($media->_id),
                'user_id' => new ObjectId($media->creator['_id']),
                'creator' => [
                    '_id' => new ObjectId($creator->_id),
                    'username' => $creator->username,
                    'avatar' => $creator->avatar,
                ],
            ]);

            $statusCode = $like ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return ['data' => $like, 'statusCode' => $statusCode];
    }

    /**
     * Responsible to unlike the given media.
     *
     * @param  Media  $media
     * @return array
     */
    public function unlike(Media $media): array
    {
        $creator = $this->getUser();

        if (($like = MediaLike::query()->media($media->_id)->creator($creator->_id)->first()) !== null) {
            $like->delete();
        }

        return [];
    }
}
