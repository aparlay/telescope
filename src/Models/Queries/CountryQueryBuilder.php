<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Str;

class CountryQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  string  $alpha2
     * @return self
     */
    public function alpha2(string $alpha2): self
    {
        return $this->where('alpha2', Str::lower($alpha2));
    }

    /**
     * @param  string  $alpha3
     * @return self
     */
    public function alpha3(string $alpha3): self
    {
        return $this->where('alpha3', Str::lower($alpha3));
    }
}