<?php

namespace Aparlay\Core\Admin\Livewire\Dashboard;

use Aparlay\Core\Admin\Services\DashboardStatsService;
use Illuminate\Contracts\View\View;

final class Stats extends BaseDashboardComponent
{
    public function mount()
    {
        $today = now()->format('Y-m-d');

        $this->dateInterval = [
            $today, $today,
        ];
    }

    public function render(): View
    {
        $stats = $this->getStats();

        if (count($stats) > 8) {
            $cardClass = 'col-md-2';
        } else {
            $cardClass = 'col-md-3';
        }

        return view('default_view::livewire.dashboard.stats', compact('stats', 'cardClass'));
    }

    private function getStats(): array
    {
        $start = $end = null;

        if (!empty($this->dateInterval) && !$this->showAllDates){
            $start = $this->startDate();
            $end = $this->endDate();
        }

        $statsService = new DashboardStatsService($start, $end);

        $analytics = $statsService->getAnalyticStats();

        if ($this->layout == self::LAYOUT_SIMPLE) {
            return $this->getSimpleStats($analytics);
        } elseif ($this->layout == self::LAYOUT_ADVANCED) {
            return array_merge(
                $this->getSimpleStats($analytics),
                $this->getAdvancedStats($analytics)
            );
        } elseif ($this->layout == self::LAYOUT_FUNNEL) {
            return $this->getFunnelStats();
        }

        return [];
    }

    private function getSimpleStats(array $results): array
    {
        return [
            [
                'label' => 'Daily Sales',
                'value' => $results['payment_orders'] ?? null,
            ],
            [
                'label' => 'Total Sales',
            ],
            [
                'label' => 'Packages',
            ],
            [
                'label' => 'Subs and Renewals',
            ],
            [
                'label' => 'Customers',
            ],
            [
                'label' => 'New Customers',
            ],
            [
                'label' => 'Featured',
            ],
            [
                'label' => 'Hidden',
            ],
            [
                'label' => 'Denied',
            ],
            [
                'label' => 'New Users',
                'value' => $results['user_registered'] ?? 'N/A',
            ],
            [
                'label' => 'Incomplete Users',
            ],
            [
                'label' => 'Registered to Paid',
            ],
            [
                'label' => 'Visitor to Paid',
                'value' => 'N/A',
            ],
            [
                'label' => 'Subscribers',
                'value' => 0,
            ],
            [
                'label' => 'Cancellations',
                'value' => 0,
            ],
            [
                'label' => 'Email Bounced',
                'value' => 0,
            ],
            [
                'label' => 'Attempts',
                'value' => 0,
            ],
            [
                'label' => 'Emails Sent',
                'value' => $results['email_sent'] ?? 'N/A',
            ],
            [
                'label' => 'Tiger 1 ratio',
                'value' => 'N/A',
            ],
        ];
    }

    private function getAdvancedStats(): array
    {
        return [
            [
                'label' => 'ITV Android $',
            ],
            [
                'label' => 'ITV iOS $',
            ],
            [
                'label' => 'ITV Web $',
            ],
            [
                'label' => 'Number of Paid Messages',
            ],
            [
                'label' => 'Users who bought Paid Messages',
            ],
            [
                'label' => 'Number of Bought Paid Messages',
            ],
            [
                'label' => 'Percent of Video',
            ],
            [
                'label' => 'Percent of Image',
            ],
            [
                'label' => 'Number of Models Send Paid Messages',
            ],
            [
                'label' => 'DAC',
            ],
            [
                'label' => 'DAM',
            ],
        ];
    }

    public function getFunnelStats(): array
    {
        return [
            [
                'label' => '[A] Visitor to lead',
                'value' => 'N/A',
            ],
            [
                'label' => '[A] Lead to Registered',
                'value' => 'N/A',
            ],
            [
                'label' => '[A] Registered to Paid',
                'value' => 'N/A',
            ],
            [
                'label' => '[A] Visitor to Paid',
                'value' => 'N/A',
            ],
            [
                'label' => '[W] Visitor to lead',
                'value' => 'N/A',
            ],
            [
                'label' => '[W] Lead to Registered',
                'value' => 'N/A',
            ],
            [
                'label' => '[W] Registered to Paid',
                'value' => 'N/A',
            ],
            [
                'label' => '[W] Visitor to Paid',
                'value' => 'N/A',
            ],
        ];
    }
}
