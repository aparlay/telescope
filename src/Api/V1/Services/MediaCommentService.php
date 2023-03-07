<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\MediaCommentLike;
use Aparlay\Core\Api\V1\Resources\MediaCommentResource;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Notifications\CommentSent;
use Illuminate\Contracts\Pagination\CursorPaginator;
use MongoDB\BSON\ObjectId;

class MediaCommentService
{
    use HasUserTrait;

    const PER_PAGE = 10;

    /**
     * @param Media $media
     *
     * @return CursorPaginator
     */
    public function list(Media $media)
    {
        return MediaComment::query()
            ->with(['parentObj'])
            ->whereNull('parent')
            ->media($media->_id)
            ->recent()
            ->cursorPaginate(self::PER_PAGE);
    }

    public function listReplies(MediaComment $mediaComment)
    {
        return MediaComment::query()
            ->parent($mediaComment->_id)
            ->recent()
            ->cursorPaginate(self::PER_PAGE);
    }

    /**
     * @param  Media  $media
     * @param         $text
     * @param  array  $additionalData
     *
     * @return MediaComment
     */
    public function create(Media $media, $text, $additionalData = []): MediaComment
    {
        $creator = $this->getUser();
        $defaultData = [
            'text' => $text,
            'media_id' => new ObjectId($media->_id),
            'user_id' => new ObjectId($media->creator['_id']),
            'creator' => [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ],
        ];

        $mediaComment = MediaComment::create([
            ...$defaultData, ...$additionalData,
        ]);

        $mediaComment->notify(new CommentSent());

        return $mediaComment;
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
        if (! $parent->first_reply) {
            $parent->first_reply = (new MediaCommentResource($mediaCommentReply))->resolve();
            $mediaCommentReply->is_first = true;
            $mediaCommentReply->save();
        }

        $parent->save();

        return $mediaCommentReply;
    }

    /**
     * @param MediaComment $mediaComment
     * @return mixed
     */
    public function delete(MediaComment $mediaComment)
    {
        MediaCommentLike::query()->comment($mediaComment->_id)->delete();

        return $mediaComment->delete();
    }
}
