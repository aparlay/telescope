<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\MediaCommentLike;
use Aparlay\Core\Api\V1\Resources\MediaCommentResource;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;

class MediaCommentLikeService
{
    use HasUserTrait;

    const PER_PAGE = 10;

    /**
     * @param $mediaCommentId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function isLikedByUser($mediaCommentId)
    {
        $this->cacheByUserId();

        $cacheKey = (new MediaCommentLike())->getCollection().':creator:'.$this->getUser()->_id;
        $likedComments = Cache::store('octane')->get($cacheKey, false);

        return ($likedComments !== false) ? in_array($mediaCommentId, explode(',', $likedComments)) :
            Redis::sismember($cacheKey, $mediaCommentId);
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
            Cache::store('octane')->forget($cacheKey);
        }

        if (Cache::store('octane')->get($cacheKey, false) !== false) {
            return;
        }
        if (! Redis::exists($cacheKey)) {
            $likedComments = MediaCommentLike::query()
                ->creator($userId)
                ->pluck('media_comment_id')
                ->toArray();

            if (empty($likedComments)) {
                $likedComments = [''];
            }

            Cache::store('octane')->put($cacheKey, implode(',', $likedComments), 300);
            Redis::sAdd($cacheKey, ...$likedComments);
            Redis::expire($cacheKey, config('app.cache.veryLongDuration'));
        }

        if (Cache::store('octane')->get($cacheKey, false) === false) {
            $likedComments = Redis::sMembers($cacheKey);

            Cache::store('octane')->put($cacheKey, implode(',', $likedComments), 300);
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

        if (!$mediaCommentLike) {
            MediaCommentLike::create([
                'media_comment_id' => new ObjectId($mediaComment->_id),
                'creator' => [
                    '_id' => new ObjectId($creator->_id),
                    'username' => $creator->username,
                    'avatar' => $creator->avatar,
                ],
            ]);
            $mediaComment->likes_count++;
            $this->refreshFirstReplyLike($mediaComment);
            $mediaComment->save();
        }

        $this->cacheByUserId(true);

        return $mediaComment;
    }

    public function unlike(MediaComment $mediaComment)
    {
        $creator = $this->getUser();
        $mediaCommentLike = MediaCommentLike::comment($mediaComment->_id)->creator($creator->_id)->first();

        if ($mediaCommentLike) {
            $mediaComment->likes_count--;
            $mediaCommentLike->delete();
            $this->refreshFirstReplyLike($mediaComment);
        }

        $this->cacheByUserId(true);

        return $mediaComment;
    }


    private function refreshFirstReplyLike(MediaComment $mediaComment)
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
