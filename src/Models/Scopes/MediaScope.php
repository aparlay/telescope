<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

trait MediaScope
{
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
     * @return Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_COMPLETED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_CONFIRMED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDenied(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_DENIED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDeleted(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_DENIED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInReview(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_IN_REVIEW);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_FAILED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAvailableForOwner(Builder $query): Builder
    {
        return $query->where('status', '$in', [
            Media::STATUS_QUEUED,
            Media::STATUS_UPLOADED,
            Media::STATUS_IN_PROGRESS,
            Media::STATUS_COMPLETED,
            Media::STATUS_CONFIRMED,
            Media::STATUS_DENIED,
            Media::STATUS_ADMIN_DELETED,
        ]);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAvailableForFollower(Builder $query): Builder
    {
        return $query->where('status', '$in', [
            Media::STATUS_CONFIRMED,
            Media::STATUS_DENIED,
        ]);
    }

    /**
     * @param  Builder  $query
     * @param  int  $status
     * @return Builder
     */
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
        $user = User::where('_id', $userId)->first();

        return $query->where('creator._id', '$in', array_column($user['followings'], '_id'));
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeNotBlockedFor(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('blocked_user_ids', '$ne', $userId);
    }

    /**
     * @param  Builder  $query
     * @param  string  $slug
     * @return Builder
     */
    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @param  string  $deviceId
     * @return Builder
     */
    public function scopeNotVisitedByUserAndDevice(Builder $query, ObjectId | string $userId, string $deviceId): Builder
    {
        $visitedIds = [];
        foreach (MediaVisit::select(['media_ids'])->user($userId)->column() as $mediaVisit) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $mediaVisit), SORT_REGULAR));
        }

        $cacheKey = 'media_visits.'.$deviceId;
        if (($visitedIdsFromCache = Cache::get($cacheKey, false)) !== false && is_array($visitedIdsFromCache)) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $visitedIdsFromCache), SORT_REGULAR));
        }

        return $query->where('_id', '$nin', $visitedIds);
    }

    /**
     * @param  Builder  $query
     * @param  string  $deviceId
     * @return Builder
     */
    public function scopeNotVisitedByDevice(Builder $query, string $deviceId): Builder
    {
        if (empty($deviceId)) {
            return $query;
        }

        $cacheKey = 'media_visits.'.$deviceId;
        if (($visitedIds = Cache::get($cacheKey, false)) !== false && is_array($visitedIds)) {
            $visitedIds = array_values(array_unique($visitedIds, SORT_REGULAR));
            $query->where('_id', '$nin', $visitedIds);
        }

        return $query;
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', Media::VISIBILITY_PUBLIC);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', Media::VISIBILITY_PRIVATE);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLicensed(Builder $query): Builder
    {
        return $query->where('is_music_licensed', true);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return mixed
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }

    /**
     * @param  Builder  $query
     * @param  UTCDateTime|null  $start
     * @param  UTCDateTime|null  $end
     * @return Builder
     */
    public function scopeDate(Builder $query, UTCDateTime $start = null, UTCDateTime $end = null): Builder
    {
        if ($start !== null && $end !== null) {
            return $query->whereBetween('created_at', [$start, $end]);
        }

        if ($start !== null) {
            return $query->where('created_at', '$gte', $start);
        }

        if ($end !== null) {
            return $query->where('created_at', '$lte', $end);
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('created_at', 'desc');
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
