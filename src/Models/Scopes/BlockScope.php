<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait BlockScope
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
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user._id', $userId);
    }

    public function scopeIsDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', true);
    }

    public function scopeIsNotDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', false);
    }

    public function scopeCountry(Builder $query, string $countryAlpha2): Builder
    {
        return $query->where('country_alpha2', $countryAlpha2);
    }

    public function scopeCountryType(Builder $query): Builder
    {
        return $query->where('user', null);
    }

    public function scopeUserType(Builder $query): Builder
    {
        return $query->where('country_alpha2', null);
    }
}
