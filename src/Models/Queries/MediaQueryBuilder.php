<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\Redis;

final class MediaQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  ObjectId|string  $creatorId
     * @return self
     */
    public function creator(ObjectId | string $creatorId): self
    {
        return $this->whereId($creatorId, 'creator._id');
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function updatedBy(ObjectId | string $userId): self
    {
        return $this->whereId($userId, 'updated_by');
    }

    /**
     * @param  ObjectId|string  $mediaId
     * @return self
     */
    public function media(ObjectId | string $mediaId): self
    {
        return $this->whereId($mediaId);
    }

    /**
     * @return self
     */
    public function completed(): self
    {
        return $this->where('status', MediaStatus::COMPLETED->value);
    }

    /**
     * @return self
     */
    public function confirmed(): self
    {
        return $this->where('status', MediaStatus::CONFIRMED->value);
    }

    /**
     * @return self
     */
    public function denied(): self
    {
        return $this->where('status', MediaStatus::DENIED->value);
    }

    /**
     * @return self
     */
    public function isDeleted(): self
    {
        return $this->where('status', MediaStatus::USER_DELETED->value);
    }

    /**
     * @return self
     */
    public function inReview(): self
    {
        return $this->where('status', MediaStatus::IN_REVIEW->value);
    }

    /**
     * @return self
     */
    public function failed(): self
    {
        return $this->where('status', MediaStatus::FAILED->value);
    }

    /**
     * @return self
     */
    public function availableForOwner(): self
    {
        return $this->whereIn('status', [
            MediaStatus::QUEUED->value,
            MediaStatus::UPLOADED->value,
            MediaStatus::IN_PROGRESS->value,
            MediaStatus::COMPLETED->value,
            MediaStatus::CONFIRMED->value,
            MediaStatus::DENIED->value,
            MediaStatus::ADMIN_DELETED->value,
        ]);
    }

    /**
     * @return self
     */
    public function availableForFollower(): self
    {
        return $this->whereIn('status', [
            MediaStatus::CONFIRMED->value,
            MediaStatus::DENIED->value,
        ]);
    }

    /**
     * @param  int  $status
     * @return self
     */
    public function status(int $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function following(ObjectId | string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);
        $user = User::user($userId)->first();

        $userIds = [];
        foreach ($user['followings'] as $following) {
            $userIds[] = $following['_id'] instanceof ObjectId ? $following['_id'] : new ObjectId($following['_id']);
        }

        return $this->whereIn('creator._id', $userIds);
    }

    /**
     * @param  ObjectId|string  $userId
     * @return self
     */
    public function notBlockedFor(ObjectId | string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->where('blocked_user_ids', '!=', $userId);
    }

    /**
     * @param  string  $slug
     * @return self
     */
    public function slug(string $slug): self
    {
        return $this->where('slug', $slug);
    }

    /**
     * @param  ObjectId|string  $userId
     * @param  string           $deviceId
     *
     * @return self
     * @throws \RedisException
     */
    public function notVisitedByUserAndDevice(ObjectId | string $userId, string $deviceId): self
    {
        $visitedIds = [];
        foreach (MediaVisit::query()->select('media_ids')->user($userId)->get()->toArray() as $mediaVisit) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $mediaVisit), SORT_REGULAR));
        }

        $cacheKey = (new MediaVisit())->getCollection().':device:'.$deviceId;
        $visitedIdsFromCache = Redis::get($cacheKey, []);
        if (! empty($visitedIdsFromCache)) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $visitedIdsFromCache), SORT_REGULAR));
        }

        return $this->whereNotIn('_id', $visitedIds);
    }

    /**
     * @param  string  $deviceId
     *
     * @return self
     * @throws \RedisException
     */
    public function notVisitedByDevice(string $deviceId): self
    {
        if (empty($deviceId)) {
            return $this;
        }

        $cacheKey = (new MediaVisit())->getCollection().':device:'.$deviceId;
        $visitedIds = Redis::get($cacheKey, []);
        if (! empty($visitedIds)) {
            $visitedIds = array_values(array_unique($visitedIds, SORT_REGULAR));
            $this->whereNotIn('_id', $visitedIds);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function public(): self
    {
        return $this->where('visibility', MediaVisibility::PUBLIC->value);
    }

    /**
     * @return self
     */
    public function private(): self
    {
        return $this->where('visibility', MediaVisibility::PRIVATE->value);
    }

    /**
     * @return self
     */
    public function licensed(): self
    {
        return $this->where('is_music_licensed', true);
    }

    /**
     * @param  ObjectId|string  $userId
     *
     * @return self
     */
    public function user(ObjectId | string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->whereId($userId, 'user_id');
    }

    /**
     * @param  string  $category
     *
     * @return $this
     */
    public function sort(string $category): self
    {
        return $this->orderBy('sort_scores.'.$category, 'desc');
    }

    /**
     * @param  string  $tag
     * @return mixed
     */
    public function hashtag(string $tag): self
    {
        return $this->where('hashtags', $tag);
    }
}
