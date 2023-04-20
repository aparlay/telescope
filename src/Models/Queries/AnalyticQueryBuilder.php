<?php

namespace Aparlay\Core\Models\Queries;

class AnalyticQueryBuilder extends EloquentQueryBuilder
{
    public function days(int $days): self
    {
        $in = [];
        for ($i = $days; $i <= 0; $i++) {
            $timestamp                             = strtotime($i . ' days midnight');
            $in[date('Y-m-d', $timestamp + 20000)] = true;
        }

        return $this->whereIn('date', array_keys($in));
    }

    /**
     * @return $this
     */
    public function filterDate(string $start, string $end): self
    {
        return $this->whereBetween('date', [$start, $end]);
    }
}
