<?php


namespace Aparlay\Core\Models\Scopes;


use Aparlay\Core\Models\Alert;
use MongoDB\BSON\ObjectId;

trait VersionScope
{

    /**
     * @param $query
     * @param $os
     * @return mixed
     */
    public function scopeOs($query, $os): mixed
    {
        return $query->where('os', $os);
    }

    /**
     * @param $query
     * @param $app
     * @return mixed
     */
    public function scopeApp($query, $app): mixed
    {
        return $query->where('app', $app);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('version', 'desc');
    }
}
