<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;

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
        return $query->where('status', MediaStatus::COMPLETED->value);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', MediaStatus::CONFIRMED->value);
    }

    public function scopeDenied(Builder $query): Builder
    {
        return $query->where('status', MediaStatus::DENIED->value);
    }

    public function scopeIsDeleted(Builder $query): Builder
    {
        return $query->where('status', MediaStatus::USER_DELETED->value);
    }

    public function scopeInReview(Builder $query): Builder
    {
        return $query->where('status', MediaStatus::IN_REVIEW->value);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', MediaStatus::FAILED->value);
    }

    public function scopeAvailableForOwner(Builder $query): Builder
    {
        return $query->whereIn('status', [
            MediaStatus::QUEUED->value,
            MediaStatus::UPLOADED->value,
            MediaStatus::IN_PROGRESS->value,
            MediaStatus::COMPLETED->value,
            MediaStatus::CONFIRMED->value,
            MediaStatus::DENIED->value,
            MediaStatus::ADMIN_DELETED->value,
        ]);
    }

    public function scopeAvailableForFollower(Builder $query): Builder
    {
        return $query->whereIn('status', [
            MediaStatus::CONFIRMED->value,
            MediaStatus::DENIED->value,
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
        return $query->where('visibility', MediaVisibility::PUBLIC->value);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', MediaVisibility::PRIVATE->value);
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

    /**
     * @return mixed
     */
    public function scopeUsername(Builder $query, string $username): Builder
    {
        return $query->where('creator.username', 'regex', new Regex('^'.$username));
    }
}
