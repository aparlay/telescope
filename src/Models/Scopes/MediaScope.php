<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;

trait MediaScope
{
    use BaseScope;
    use DateScope;

    /**
     * @param  Builder          $query
     * @param  ObjectId|string  $creatorId
     *
     * @return Builder
     */
    public function scopeCreator(Builder $query, ObjectId|string $creatorId): Builder
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $query->where('creator._id', $creatorId);
    }

    /**
     * @param  Builder          $query
     * @param  ObjectId|string  $userId
     *
     * @return Builder
     */
    public function scopeUpdatedBy(Builder $query, ObjectId|string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('updated_by', $userId);
    }

    /**
     * @param  Builder          $query
     * @param  ObjectId|string  $mediaId
     *
     * @return Builder
     */
    public function scopeMedia(Builder $query, ObjectId|string $mediaId): Builder
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('_id', $mediaId);
    }

    /**
     * @param  Builder  $query
     * @param  array    $mediaIds
     *
     * @return Builder
     */
    public function scopeMedias(Builder $query, array $mediaIds): Builder
    {
        foreach ($mediaIds as $key => $mediaId) {
            $mediaIds[$key] = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);
        }

        return $query->whereIn('_id', $mediaIds);
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
     * @param  Builder          $query
     * @param  ObjectId|string  $userId
     *
     * @return Builder
     */
    public function scopeFollowing(Builder $query, ObjectId|string $userId): Builder
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
     * @param  Builder          $query
     * @param  ObjectId|string  $userId
     *
     * @return Builder
     */
    public function scopeNotBlockedFor(Builder $query, ObjectId|string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('blocked_user_ids', '!=', $userId);
    }

    /**
     * @param  Builder          $query
     * @param  ObjectId|string  $userId
     *
     * @return Builder
     */
    public function scopeBlockedFor(Builder $query, ObjectId|string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('blocked_user_ids', $userId);
    }

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @param  Builder          $query
     * @param  ObjectId|string  $userId
     * @param  string           $deviceId
     *
     * @return Builder
     * @throws \RedisException
     */
    public function scopeNotVisitedByUserAndDevice(Builder $query, ObjectId|string $userId, string $deviceId): Builder
    {
        $visitedIds = [];
        foreach (MediaVisit::query()->select('media_ids')->user($userId)->get()->toArray() as $mediaVisit) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $mediaVisit), SORT_REGULAR));
        }

        $cacheKey = (new MediaVisit())->getCollection().':device:'.$deviceId;
        $visitedIdsFromCache = Cache::store('redis')->get($cacheKey, []);
        if (! empty($visitedIdsFromCache)) {
            $visitedIds = array_values(array_unique(array_merge($visitedIds, $visitedIdsFromCache), SORT_REGULAR));
        }

        return $query->whereNotIn('_id', $visitedIds);
    }

    /**
     * @param  Builder  $query
     * @param  string   $deviceId
     *
     * @return Builder
     * @throws \RedisException
     */
    public function scopeNotVisitedByDevice(Builder $query, string $deviceId): Builder
    {
        if (empty($deviceId)) {
            return $query;
        }

        $cacheKey = (new MediaVisit())->getCollection().':device:'.$deviceId;
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
     * @param  Builder          $query
     * @param  ObjectId|string  $userId
     *
     * @return mixed
     */
    public function scopeUser(Builder $query, ObjectId|string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }

    /**
     * @param  Builder  $query
     * @param  string   $category
     *
     * @return mixed
     */
    public function scopeSort(Builder $query, string $category = 'default'): Builder
    {
        return $query->orderBy('sort_scores.'.$category, 'desc');
    }

    /**
     * @param  Builder  $query
     * @param  string   $tag
     *
     * @return mixed
     */
    public function scopeHashtag(Builder $query, string $tag): Builder
    {
        return $query->where('hashtags', $tag);
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    public function scopeExplicit(Builder $query): Builder
    {
        return $query->where('scores', ['$elemMatch' => ['type' => 'skin', 'score' => ['$gte' => config('app.media.explicit_skin_score')]]]);
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    public function scopeTopless(Builder $query): Builder
    {
        return $query->where('scores', ['$elemMatch' => ['type' => 'skin', 'score' => ['$gte' => config('app.media.topless_skin_score')]]]);
    }

    /**
     * @param  Builder    $query
     * @param  array|int  $genders
     *
     * @return mixed
     */
    public function scopeGenderContent(Builder $query, array|int $genders): Builder
    {
        if (is_array($genders)) {
            $genders = array_map('intval', $genders);
            $genders = array_intersect($genders, MediaContentGender::getAllValues());
        } elseif (is_int($genders) && in_array($genders, MediaContentGender::getAllValues())) {
            $genders = [$genders];
        } else {
            return $query;
        }

        return $query->whereIn('content_gender', $genders);
    }
}
