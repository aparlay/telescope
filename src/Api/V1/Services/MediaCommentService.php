<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use MongoDB\BSON\ObjectId;

class MediaCommentService
{
    use HasUserTrait;

    /**
     * @param Media $media
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(Media $media)
    {
        return MediaComment::query()
            ->with('lastRepliesObjs')
            ->whereNull('parent')
            ->media($media->_id)
            ->latest()
            ->paginate();
    }


    public function listReplies(MediaComment $mediaComment)
    {
        return MediaComment::query()
            ->parent($mediaComment->_id)
            ->paginate();
    }



    /**
     * @param Media $media
     * @param $text
     * @return MediaComment
     */
    public function create(Media $media, $text, MediaComment $parent = null): MediaComment
    {
        $creator = $this->getUser();

        $mediaComment = MediaComment::make([
            'text' => $text,
            'media_id' => new ObjectId($media->_id),
            'user_id' => new ObjectId($creator->_id),
            'creator' => [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ],
        ]);

        if ($parent) {
            $mediaComment->parent = [
                '_id' => new ObjectId($parent->_id)
            ];
        }

        $mediaComment->save();

        return $mediaComment;
    }

    /**
     * @param MediaComment $mediaComment
     * @return mixed
     */
    public function delete(MediaComment $mediaComment)
    {
        return $mediaComment->delete();
    }
}
