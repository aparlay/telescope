<?php

namespace Aparlay\Core\Models\Scopes;

trait SettingScope
{
    use BaseScope;
    use DateScope;

    public function scopeSettingGroup($query)
    {
        return $query->groupBy('group')->get(['group']);
    }

    public function scopeTitle($query, $title)
    {
        return $query->where('title', $title);
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
