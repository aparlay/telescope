<?php

namespace Aparlay\Core\Models\Scopes;

trait VersionScope
{
    use BaseScope;

    public function scopeOs($query, $os): mixed
    {
        return $query->where('os', $os);
    }

    public function scopeApp($query, $app): mixed
    {
        return $query->where('app', $app);
    }

    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('version', 'desc');
    }
}
