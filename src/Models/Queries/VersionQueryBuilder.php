<?php

namespace Aparlay\Core\Models\Queries;

class VersionQueryBuilder extends EloquentQueryBuilder
{
    public function os($os): self
    {
        return $this->where('os', $os);
    }

    public function app($app): self
    {
        return $this->where('app', $app);
    }

    public function recentFirst(): self
    {
        return $this->orderBy('version', 'desc');
    }
}
