<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Resources\MediaCommentResource;
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
            ->with(['parentObj'])
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
    public function create(Media $media, $text, $additionalData = []): MediaComment
    {
        $creator = $this->getUser();
        $defaultData = [
            'text' => $text,
            'media_id' => new ObjectId($media->_id),
            'user_id' => new ObjectId($creator->_id),
            'creator' => [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ],
        ];

        return MediaComment::create([
            ...$defaultData, ...$additionalData,
        ]);
    }

    public function createReply(MediaComment $replyTo, $text)
    {
        /** @var Media $mediaObj */
        $mediaObj = $replyTo->mediaObj;
        $replyToUser = $replyTo->creatorObj;
        $parent = $replyTo->parentObj ?? $replyTo;

        $additionalData = [
            'reply_to_user' => [
                '_id' => new ObjectId($replyToUser->_id),
                'username' => $replyToUser->username,
                'avatar' => $replyToUser->avatar,
            ],
            'parent' => [
                '_id' => new ObjectId($parent->_id),
            ],
        ];

        $mediaCommentReply = $this->create($mediaObj, $text, $additionalData);
        $parent->replies_count++;
        $parent->last_reply = (new MediaCommentResource($mediaCommentReply))->resolve();
        $parent->save();

        return $mediaCommentReply;
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
