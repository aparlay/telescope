<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\UTCDateTime;

trait AnalyticScope
{
    public function scopeDays(Builder $query, int $days): Builder
    {
        $in = [];
        for ($i = $days; $i <= 0; $i++) {
            $timestamp = strtotime($i.' days midnight');
            $in[date('Y-m-d', $timestamp + 20000)] = true;
        }

        return $query->whereIn('date', array_keys($in));
    }

    public function scopeDate(Builder $query, UTCDateTime $start, UTCDateTime $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
