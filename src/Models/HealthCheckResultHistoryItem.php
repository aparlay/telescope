<?php

namespace Aparlay\Core\Models;

use Aparlay\Core\Models\Queries\EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Spatie\Health\ResultStores\EloquentHealthResultStore;

/**
 * @property \Carbon\Carbon $created_at
 * @property string $batch
 * @property string $ended_at
 * @property string $notification_message
 * @property string $short_summary
 * @property array<string, mixed> $meta
 * @property string $status
 * @property string $check_name
 * @property string $check_label
 */
class HealthCheckResultHistoryItem extends BaseModel
{
    use HasFactory;
    use MassPrunable;

    protected $guarded = [];

    /**
     * @var array<string,string>
     */
    public $casts = [
        'meta' => 'array',
        'started_failing_at' => 'timestamp',
    ];

    public function prunable(): EloquentQueryBuilder|HealthCheckResultHistoryItem
    {
        $days = config('health.result_stores.'.EloquentHealthResultStore::class.'.keep_history_for_days') ?? 5;

        return static::where('created_at', '<=', now()->subDays($days));
    }
}
