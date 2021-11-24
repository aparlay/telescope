<?php

namespace Aparlay\Core\Models\Scopes;

trait SettingScope
{
    use BaseScope;

    public function scopeSettingGroup($query)
    {
        return $query->groupBy('group')->get(['group']);
    }
}
