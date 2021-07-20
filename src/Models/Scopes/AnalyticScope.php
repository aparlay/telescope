<?php


namespace Aparlay\Core\Models\Scopes;


use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\UTCDateTime;

trait AnalyticScope
{
    /**
     * @param  Builder  $query
     * @param  int  $days
     * @return Builder
     */
    public function scopeDays(Builder $query, int $days): Builder
    {
        $in = [];
        for ($i = $days; $i <= 0; $i++) {
            $timestamp = strtotime($i.' days midnight');
            $in[date('Y-m-d', $timestamp + 20000)] = true;
        }

        return $query->where('date', '$in', array_keys($in));
    }

    /**
     * @param  Builder  $query
     * @param  UTCDateTime  $start
     * @param  UTCDateTime  $end
     * @return Builder
     */
    public function scopeDate(Builder $query, UTCDateTime $start, UTCDateTime $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
