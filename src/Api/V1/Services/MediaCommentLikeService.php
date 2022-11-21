<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\MediaCommentLike;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Redis;
use MongoDB\BSON\ObjectId;

class MediaCommentLikeService
{
    use HasUserTrait;

    /**
     * @param string $mediaCommentId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function isLikedByUser(string $mediaCommentId): bool
    {
        $this->cacheByUserId();

        $cacheKey = (new MediaCommentLike())->getCollection().':creator:'.$this->getUser()->_id;

        return Redis::sismember($cacheKey, $mediaCommentId);
    }

    /**
     * @param bool $refresh
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cacheByUserId(bool $refresh = false): void
    {
        $userId = $this->getUser()->_id;
        $cacheKey = (new MediaCommentLike())->getCollection().':creator:'.$userId;

        if ($refresh) {
            Redis::del($cacheKey);
        }

        if (! Redis::exists($cacheKey)) {
            $likedCommentIds = MediaCommentLike::query()
                ->creator($userId)
                ->pluck('media_comment_id')
                ->toArray();

            if (empty($likedCommentIds)) {
                $likedCommentIds = [''];
            }

            $likedCommentIds = array_map('strval', $likedCommentIds);

            Redis::sAdd($cacheKey, ...$likedCommentIds);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }
    }

    /**
     * @param MediaComment $mediaComment
     * @return MediaComment
     */
    public function like(MediaComment $mediaComment)
    {
        $creator = $this->getUser();
        $mediaCommentLike = MediaCommentLike::comment($mediaComment->_id)->creator($creator->_id)->first();

        if (! $mediaCommentLike) {
            MediaCommentLike::create([
                'media_comment_id' => new ObjectId($mediaComment->_id),
                'created_by' => new ObjectId($creator->_id),
                'creator' => [
                    '_id' => new ObjectId($creator->_id),
                    'username' => $creator->username,
                    'avatar' => $creator->avatar,
                ],
            ]);
            $mediaComment->likes_count++;
            $this->refreshFirstReplyLikes($mediaComment);
            $mediaComment->save();
            $this->cacheByUserId(true);
        }

        return $mediaComment;
    }

    public function unlike(MediaComment $mediaComment)
    {
        $creator = $this->getUser();
        $mediaCommentLike = MediaCommentLike::comment($mediaComment->_id)->creator($creator->_id)->first();

        if ($mediaCommentLike) {
            $mediaComment->likes_count--;
            $mediaComment->save();
            $mediaCommentLike->delete();
            $this->refreshFirstReplyLikes($mediaComment);
            $this->cacheByUserId(true);
        }

        return $mediaComment;
    }

    private function refreshFirstReplyLikes(MediaComment $mediaComment)
    {
        if ($mediaComment->is_first && $mediaComment->parentObj) {
            $firstReply = $mediaComment->parentObj->first_reply;
            $firstReply['likes_count'] = $mediaComment->likes_count;
            $mediaComment->parentObj->first_reply = $firstReply;
            $mediaComment->parentObj->save();
        }

        return $mediaComment;
    }
}
