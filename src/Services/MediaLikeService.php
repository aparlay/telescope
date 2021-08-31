<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Repositories\MediaLikeRepository;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class MediaLikeService
{
    protected $mediaLikeRepository;

    public function __construct()
    {
        $this->mediaLikeRepository = new MediaLikeRepository(new MediaLike());
    }

    /**
     * Responsible to create like for given media.
     *
     * @param Media $media
     * @return array
     */
    public function create(Media $media)
    {
        $statusCode = Response::HTTP_OK;
        if (($like = $this->mediaLikeRepository->isLiked($media)) === null) {
            $like = $this->mediaLikeRepository->create([
                'media_id' => new ObjectId($media->_id),
                'user_id' => new ObjectId(auth()->user()->_id),
            ]);
            $statusCode = Response::HTTP_CREATED;
        }

        return ['data' => $like, 'statusCode' => $statusCode];
    }

    /**
     * Responsible to unlike the given media.
     *
     * @param Media
     * @return bool
     */
    public function unlike(Media $media)
    {
        if (($like = $this->mediaLikeRepository->isLiked($media)) === null) {
            throw new BlockedException('No Record Found', null, null, Response::HTTP_NOT_FOUND);
        }
        $like->delete();
    }
}
