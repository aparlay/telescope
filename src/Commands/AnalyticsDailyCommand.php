<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Admin\Services\AnalyticsCalculatorService;
use Aparlay\Core\Helpers\DT;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class AnalyticsDailyCommand extends Command implements Isolatable
{
    public $signature = 'analytics:daily {day=0}';

    public $description = 'This command is responsible for creating Daily Analytics Report';

    public function handle(): int
    {
        $daysBefore = $this->argument('day');
        $stringTime = ($daysBefore === '0') ? 'midnight' : '-'.$daysBefore.' days midnight';
        $endingTime = ($daysBefore === '0') ? 'tomorrow' : '-'.($daysBefore - 1).' days midnight';
        $stringTimestamp = strtotime($stringTime);
        $endingTimestamp = strtotime($endingTime);

        $startUtc = DT::timestampToUtc($stringTimestamp);
        $endUtc = DT::timestampToUtc($endingTimestamp - 1);

        $analytics = app()->make(AnalyticsCalculatorService::class);
        $analytics->calculateAnalytics($startUtc, $endUtc);

        return self::SUCCESS;
    }
}
