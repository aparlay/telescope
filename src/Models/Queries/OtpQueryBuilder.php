<?php

namespace Aparlay\Core\Models\Queries;

class OtpQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  string  $identity
     * @return self
     */
    public function identity(string $identity): self
    {
        return $this->where('identity', $identity);
    }

    /**
     * @param  string  $otp
     * @return self
     */
    public function otp(string $otp): self
    {
        return $this->where('otp', $otp);
    }

    /**
     * @param  bool  $checkValidated
     * @return self
     */
    public function validated(bool $checkValidated): self
    {
        return $this->where('validated', $checkValidated);
    }

    /**
     * @param  int $limit
     * @return self
     */
    public function remainingAttempt(int $limit): self
    {
        return $this->whereIn('incorrect', range(0, $limit));
    }

    /**
     * @return self
     */
    public function recentFirst(): self
    {
        return $this->orderBy('created_at', 'desc');
    }
}
