<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait OtpScope
{
    use BaseScope;

    /**
     * @param  Builder  $query
     * @param  string  $identity
     * @return mixed
     */
    public static function scopeIdentity(Builder $query, string $identity): Builder
    {
        return $query->where('identity', $identity);
    }

    /**
     * @param  Builder  $query
     * @param  string  $otp
     * @return mixed
     */
    public function scopeOtp(Builder $query, string $otp): Builder
    {
        return $query->where('otp', $otp);
    }

    /**
     * @param  Builder  $query
     * @param  bool  $checkValidated
     * @param  int $limit
     * @return mixed
     */
    public function scopeValidated(Builder $query, bool $checkValidated): Builder
    {
        return $query->where('validated', $checkValidated);
    }

    /**
     * @param  Builder  $query
     * @param  int $limit
     * @return mixed
     */
    public function scopeRemainingAttempt(Builder $query, int $limit): Builder
    {
        return $query->whereIn('incorrect', range(0, $limit));
    }

    /**
     * @param  Builder  $query
     * @param $query
     * @return mixed
     */
    public function scopeRecentFirst(Builder $query): mixed
    {
        return $query->orderBy('created_at', 'desc');
    }
}
