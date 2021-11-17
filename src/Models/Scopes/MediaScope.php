<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

trait MediaScope
{
    use BaseScope;

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $creatorId
     * @return Builder
     */
    public function scopeCreator(Builder $query, ObjectId | string $creatorId): Builder
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $query->where('creator._id', $creatorId);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $mediaId
     * @return Builder
     */
    public function scopeMedia(Builder $query, ObjectId | string $mediaId): Builder
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('_id', $mediaId);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_COMPLETED);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_CONFIRMED);
    }

    public function scopeDenied(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_DENIED);
    }

    public function scopeIsDeleted(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_USER_DELETED);
    }

    public function scopeInReview(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_IN_REVIEW);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_FAILED);
    }

    public function scopeAvailableForOwner(Builder $query): Builder
    {
        return $query->whereIn('status', [
            Media::STATUS_QUEUED,
            Media::STATUS_UPLOADED,
            Media::STATUS_IN_PROGRESS,
            Media::STATUS_COMPLETED,
            Media::STATUS_CONFIRMED,
            Media::STATUS_DENIED,
            Media::STATUS_ADMIN_DELETED,
        ]);
    }

    public function scopeAvailableForFollower(Builder $query): Builder
    {
        return $query->whereIn('status', [
            Media::STATUS_CONFIRMED,
            Media::STATUS_DENIED,
        ]);
    }

    public function scopeStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeFollowing(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);
        $user = User::user($userId)->first();

        $userIds = [];
        foreach ($user['followings'] as $following) {
            $userIds[] = $following['_id'] instanceof ObjectId ? $following['_id'] : new ObjectId($following['_id']);
        }

        return $query->whereIn('creator._id', $userIds);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeNotBlockedFor(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('blocked_user_ids', '!=', $userId);
    }

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @param  string  $deviceId
     * @return Builder
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function scopeNotVisitedByUserAndDevice(Builder $query, ObjectId | string $userId, string $deviceId): Builder
    {
        $visitedIds = [];
        foreach (MediaVisit::select('media_ids')->user($userId)->get()->toArray() as $mediaVisit) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $mediaVisit), SORT_REGULAR));
        }

        $cacheKey = (new MediaVisit())->getCollection().':'.$deviceId;
        $visitedIdsFromCache = Cache::store('redis')->get($cacheKey, []);
        if (! empty($visitedIdsFromCache)) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $visitedIdsFromCache), SORT_REGULAR));
        }

        return $query->whereNotIn('_id', $visitedIds);
    }

    /**
     * @param  Builder  $query
     * @param  string  $deviceId
     * @return Builder
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function scopeNotVisitedByDevice(Builder $query, string $deviceId): Builder
    {
        if (empty($deviceId)) {
            return $query;
        }

        $cacheKey = (new MediaVisit())->getCollection().':'.$deviceId;
        $visitedIds = Cache::store('redis')->get($cacheKey, []);
        if (! empty($visitedIds)) {
            $visitedIds = array_values(array_unique($visitedIds, SORT_REGULAR));
            $query->whereNotIn('_id', $visitedIds);
        }

        return $query;
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', Media::VISIBILITY_PUBLIC);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', Media::VISIBILITY_PRIVATE);
    }

    public function scopeLicensed(Builder $query): Builder
    {
        return $query->where('is_music_licensed', true);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     *
     * @return mixed
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeSort($query): mixed
    {
        return $query->orderBy('sort_score', 'desc');
    }
}
