<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use MongoDB\BSON\ObjectId;

class MediaCommentService
{
    use HasUserTrait;

    const PER_PAGE = 10;

    /**
     * @param Media $media
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(Media $media)
    {
        return MediaComment::query()
            ->with(['lastRepliesObjs', 'parentObj'])
            ->whereNull('parent')
            ->media($media->_id)
            ->latest('_id')
            ->cursorPaginate(self::PER_PAGE);
    }

    public function listReplies(MediaComment $mediaComment)
    {
        return MediaComment::query()
            ->parent($mediaComment->_id)
            ->latest('_id')
            ->cursorPaginate(self::PER_PAGE);
    }

    /**
     * @param Media $media
     * @param $text
     * @return MediaComment
     */
    public function create(Media $media, $text, MediaComment $replyTo = null): MediaComment
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

        if ($replyTo) {
            $replyToUser = $replyTo->creatorObj;

            $mediaComment->reply_to_user =  [
                '_id' => new ObjectId($replyToUser->_id),
                'username' => $replyToUser->username,
                'avatar' => $replyToUser->avatar,
            ];

            if ($replyTo->parentObj) {
                $mediaComment->parent = [
                    '_id' => new ObjectId($replyTo->parentObj->_id),
                ];
            } else {
                $mediaComment->parent = [
                    '_id' => new ObjectId($replyTo->_id),
                ];
            }

            $mediaComment->replies_count++;
            $mediaComment->save();
        }

        $mediaComment->load('parentObj');
        $mediaComment->save();

        return $mediaComment;
    }

    /**
     * @param MediaComment $mediaComment
     * @return mixed
     */
    public function delete(MediaComment $mediaComment)
    {
        if ($mediaComment->parentObj) {
            $mediaComment->parentObj->replies_count--;
            $mediaComment->parentObj->save();
        }

        return $mediaComment->delete();
    }
}
