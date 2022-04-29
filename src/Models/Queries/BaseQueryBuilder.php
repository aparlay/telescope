<?php
namespace Aparlay\Core\Models\Queries;

use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

class BaseQueryBuilder extends Builder
{
    /**
     * @param $filters
     * @return $this
     */
    public function filter($filters): self
    {
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $this->where($key, (int) $filter);
            } else {
                $this->where($key, 'regex', new Regex('^'.$filter));
            }
        }

        return $this;
    }

    /**
     * @param $sorts
     * @return $this
     */
    public function sortBy($sorts): self
    {
        foreach ($sorts as $field => $direction) {
            $this->orderBy($field, $direction);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function recentFirst(): self
    {
        return $this->orderBy('created_at', 'desc');
    }

    /**
     * @param  UTCDateTime  $date
     * @return $this
     */
    public function since(UTCDateTime $date): self
    {
        return $this->where('created_at', ['$gte' => $date]);
    }
}
