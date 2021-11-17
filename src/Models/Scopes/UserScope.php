<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

trait UserScope
{
    use BaseScope;
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
     * @param  Builder  $query
     * @param  array  $usernames
     * @return mixed
     */
    public function scopeUsernames(Builder $query, array $usernames): Builder
    {
        return $query->whereIn('username', $usernames);
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
        return $query->whereIn('status', [
            User::STATUS_PENDING,
            User::STATUS_VERIFIED,
            User::STATUS_ACTIVE,
        ]);
    }

    public function scopeDisable(Builder $query): Builder
    {
        return $query->whereIn('status', [
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
}
