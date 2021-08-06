<?php

namespace Aparlay\Core\Models\Scopes;

trait VersionScope
{
    /**
     * @param $query
     * @param $os
     */
    public function scopeOs($query, $os): mixed
    {
        return $query->where('os', $os);
    }

    /**
     * @param $query
     * @param $app
     */
    public function scopeApp($query, $app): mixed
    {
        return $query->where('app', $app);
    }

    /**
     * @param $query
     */
    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('version', 'desc');
    }
}
