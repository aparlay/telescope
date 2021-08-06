<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

trait UserScope
{
    /**
     * @return mixed
     */
    public function scopeEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * @return mixed
     */
    public function scopePhoneNumber(Builder $query, string $phone): Builder
    {
        return $query->where('phone_number', $phone);
    }

    /**
     * @return mixed
     */
    public function scopeUsername(Builder $query, string $username): Builder
    {
        return $query->where('username', $username);
    }

    /**
     * @return mixed
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('type', User::TYPE_ADMIN);
    }

    /**
     * @return mixed
     */
    public function scopePendingSince(Builder $query, UTCDateTime $since): Builder
    {
        return $query->where('status', User::STATUS_PENDING)
            ->where('hide_moderation_till', '$gte', $since);
    }

    public function scopeEnable(Builder $query): Builder
    {
        return $query->where('status', '$in', [
            User::STATUS_PENDING,
            User::STATUS_VERIFIED,
            User::STATUS_ACTIVE,
        ]);
    }

    public function scopeDisable(Builder $query): Builder
    {
        return $query->where('status', '$in', [
            User::STATUS_SUSPENDED,
            User::STATUS_BLOCKED,
            User::STATUS_DEACTIVATED,
        ]);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', User::VISIBILITY_PRIVATE);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', User::VISIBILITY_PUBLIC);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', User::STATUS_PENDING);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', User::STATUS_ACTIVE);
    }

    /**
     * @param ObjectId|string $userId
     *
     * @return mixed
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('_id', $userId);
    }

    public function scopeDate(Builder $query, UTCDateTime $start = null, UTCDateTime $end = null): Builder
    {
        if (null !== $start && null !== $end) {
            return $query->whereBetween('created_at', [$start, $end]);
        }

        if (null !== $start) {
            return $query->where('created_at', '$gte', $start);
        }

        if (null !== $end) {
            return $query->where('created_at', '$lte', $end);
        }

        return $query;
    }

    /**
     * @param $query
     */
    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('created_at', 'desc');
    }
}
