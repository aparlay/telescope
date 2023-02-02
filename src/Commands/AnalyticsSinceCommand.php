<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Admin\Services\AnalyticsCalculatorService;
use Aparlay\Core\Helpers\DT;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class AnalyticsSinceCommand extends Command implements Isolatable
{
    public $signature = 'analytics:since {days=90}';

    public $description = 'This command is responsible for creating Two Month Analytics Report';

    public function handle()
    {
        $daysBefore = (int) $this->argument('days');
        for ($i = -$daysBefore; $i <= 0; $i++) {
            $timestamp = strtotime($i.' days midnight');
            $startUtc = DT::timestampToUtc($timestamp);
            $endUtc = DT::timestampToUtc($timestamp + 86400);

            $analytics = app()->make(AnalyticsCalculatorService::class);
            $analytics->calculateAnalytics($startUtc, $endUtc, true, false);

            $date = $startUtc->toDateTime()->format('Y-m-d');

            $this->line('<fg=yellow;options=bold>'.$date.' analytics stored.'.PHP_EOL.'</>');
        }

        return self::SUCCESS;
    }
}
