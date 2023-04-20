<?php

namespace Aparlay\Core\Models\Queries;

use Str;

class CountryQueryBuilder extends EloquentQueryBuilder
{
    public function alpha2(string $alpha2): self
    {
        return $this->where('alpha2', Str::lower($alpha2));
    }

    public function alpha3(string $alpha3): self
    {
        return $this->where('alpha3', Str::lower($alpha3));
    }
}
