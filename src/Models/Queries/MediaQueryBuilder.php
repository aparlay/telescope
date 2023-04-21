<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;
use Psr\SimpleCache\InvalidArgumentException;

final class MediaQueryBuilder extends EloquentQueryBuilder
{
    public function creator(ObjectId|string $creatorId): self
    {
        return $this->whereId($creatorId, 'creator._id');
    }

    public function updatedBy(ObjectId|string $userId): self
    {
        return $this->whereId($userId, 'updated_by');
    }

    public function media(ObjectId|string $mediaId): self
    {
        return $this->whereId($mediaId);
    }

    public function completed(): self
    {
        return $this->where('status', MediaStatus::COMPLETED->value);
    }

    public function confirmed(): self
    {
        return $this->where('status', MediaStatus::CONFIRMED->value);
    }

    public function denied(): self
    {
        return $this->where('status', MediaStatus::DENIED->value);
    }

    public function isDeleted(): self
    {
        return $this->where('status', MediaStatus::USER_DELETED->value);
    }

    public function inReview(): self
    {
        return $this->where('status', MediaStatus::IN_REVIEW->value);
    }

    public function failed(): self
    {
        return $this->where('status', MediaStatus::FAILED->value);
    }

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

    public function availableForFollower(): self
    {
        return $this->whereIn('status', [
            MediaStatus::CONFIRMED->value,
            MediaStatus::DENIED->value,
        ]);
    }

    public function status(int $status): self
    {
        return $this->where('status', $status);
    }

    public function following(ObjectId|string $userId): self
    {
        $userId  = $userId instanceof ObjectId ? $userId : new ObjectId($userId);
        $user    = User::user($userId)->first();

        $userIds = [];
        foreach ($user['followings'] as $following) {
            $userIds[] = $following['_id'] instanceof ObjectId ? $following['_id'] : new ObjectId($following['_id']);
        }

        return $this->whereIn('creator._id', $userIds);
    }

    public function notBlockedFor(ObjectId|string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->where('blocked_user_ids', '!=', $userId);
    }

    public function slug(string $slug): self
    {
        return $this->where('slug', $slug);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function notVisitedByUserAndDevice(ObjectId|string $userId, string $deviceId): self
    {
        $visitedIds          = [];
        foreach (MediaVisit::query()->select('media_ids')->user($userId)->get()->toArray() as $mediaVisit) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $mediaVisit), SORT_REGULAR));
        }

        $cacheKey            = (new MediaVisit())->getCollection() . ':device:' . $deviceId;
        $visitedIdsFromCache = Cache::store('redis')->get($cacheKey, []);
        if (!empty($visitedIdsFromCache)) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $visitedIdsFromCache), SORT_REGULAR));
        }

        return $this->whereNotIn('_id', $visitedIds);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function notVisitedByDevice(string $deviceId): self
    {
        if (empty($deviceId)) {
            return $this;
        }

        $cacheKey   = (new MediaVisit())->getCollection() . ':device:' . $deviceId;
        $visitedIds = Cache::store('redis')->get($cacheKey, []);
        if (!empty($visitedIds)) {
            $visitedIds = array_values(array_unique($visitedIds, SORT_REGULAR));
            $this->whereNotIn('_id', $visitedIds);
        }

        return $this;
    }

    public function public(): self
    {
        return $this->where('visibility', MediaVisibility::PUBLIC->value);
    }

    public function private(): self
    {
        return $this->where('visibility', MediaVisibility::PRIVATE->value);
    }

    public function licensed(): self
    {
        return $this->where('is_music_licensed', true);
    }

    public function user(ObjectId|string $userId): self
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $this->whereId($userId, 'user_id');
    }

    /**
     * @return $this
     */
    public function sort(string $category): self
    {
        return $this->orderBy('sort_scores.' . $category, 'desc');
    }

    /**
     * @return mixed
     */
    public function hashtag(string $tag): self
    {
        return $this->where('hashtags', $tag);
    }
}
