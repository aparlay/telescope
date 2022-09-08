<?php

namespace Aparlay\Core\Models\Queries;

use Aparlay\Core\Models\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class AnalyticQueryBuilder extends EloquentQueryBuilder
{
    /**
     * @param  int  $days
     * @return self
     */
    public function days(int $days): self
    {
        $in = [];
        for ($i = $days; $i <= 0; $i++) {
            $timestamp = strtotime($i.' days midnight');
            $in[date('Y-m-d', $timestamp + 20000)] = true;
        }

        return $this->whereIn('date', array_keys($in));
    }

    /**
     * @param  string  $start
     * @param  string  $end
     * @return $this
     */
    public function filterDate(string $start, string $end): self
    {
        return $this->whereBetween('date', [$start, $end]);
    }
}
