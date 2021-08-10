<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Otp;
use Illuminate\Database\Eloquent\Builder;

trait OtpScope
{
    /**
     * @param  Builder  $query
     * @param  string  $identity
     * @return mixed
     */
    public static function scopeFilterByIdentity(Builder $query, string $identity): Builder
    {
        return $query->where(['identity' => $identity]);
    }

    /**
     * @param  Builder  $query
     * @param  string  $identity
     * @return mixed
     */
    public function scopeOtpIncorrect(Builder $query, string $identity): Builder
    {
        return $query->where('identity', $identity);
    }

    /**
     * @param  Builder  $query
     * @param  string  $otp
     * @param  string  $identity
     * @param  bool  $checkValidated
     * @param  int $limit
     * @return mixed
     */
    public function scopeOtpIdentity(Builder $query, string $otp, string $identity, bool $checkValidated, int $limit): Builder
    {
        return $query->where([
            'otp' => $otp,
            'identity' => $identity,
            'validated' => $checkValidated,
            'incorrect' => ['$in' => range(0, $limit)],
        ]);
    }
}
