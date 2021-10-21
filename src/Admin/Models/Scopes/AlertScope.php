<?php

namespace Aparlay\Core\Admin\Models\Scopes;

trait AlertScope
{
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
