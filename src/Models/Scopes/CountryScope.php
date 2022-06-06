<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Str;

trait CountryScope
{
    use BaseScope;


    public function scopeAlpha2(Builder $query, string $alpha2): Builder
    {
        return $query->where('alpha2', Str::lower($alpha2));
    }
}
