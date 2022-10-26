<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Admin\Services\AnalyticsCalculatorService;
use Aparlay\Core\Helpers\DT;
use Illuminate\Console\Command;

class AnalyticsTwoMonthCommand extends Command
{
    public $signature = 'analytics:two-months';

    public $description = 'This command is responsible for creating Two Month Analytics Report';

    public function handle()
    {
        for ($i = -90; $i <= 0; $i++) {
            $timestamp = strtotime($i.' days midnight');
            $startUtc = DT::timestampToUtc($timestamp);
            $endUtc = DT::timestampToUtc($timestamp + 86400);

            AnalyticsCalculatorService::calculateAnalytics($startUtc, $endUtc);

            $date = $startUtc->toDateTime()->format('Y-m-d');

            $this->line('<fg=yellow;options=bold>'.$date.' analytics stored.'.PHP_EOL.'</>');
        }

        return self::SUCCESS;
    }
}
