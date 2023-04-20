<?php

namespace Aparlay\Core\Models\Queries;

class OtpQueryBuilder extends EloquentQueryBuilder
{
    public function identity(string $identity): self
    {
        return $this->where('identity', $identity);
    }

    public function otp(string $otp): self
    {
        return $this->where('otp', $otp);
    }

    public function validated(bool $checkValidated): self
    {
        return $this->where('validated', $checkValidated);
    }

    public function remainingAttempt(int $limit): self
    {
        return $this->whereIn('incorrect', range(0, $limit));
    }

    public function recentFirst(): self
    {
        return $this->orderBy('created_at', 'desc');
    }
}
