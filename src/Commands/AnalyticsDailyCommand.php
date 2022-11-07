<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Admin\Services\AnalyticsCalculatorService;
use Aparlay\Core\Helpers\DT;
use Illuminate\Console\Command;

class AnalyticsDailyCommand extends Command
{
    public $signature = 'analytics:daily';

    public $description = 'This command is responsible for creating Daily Analytics Report';

    public function handle(): int
    {
        $timestamp = strtotime('midnight');

        $startUtc = DT::timestampToUtc($timestamp);
        $endUtc = DT::timestampToUtc($timestamp + 86400);

        AnalyticsCalculatorService::calculateAnalytics($startUtc, $endUtc);

        return self::SUCCESS;
    }
}
