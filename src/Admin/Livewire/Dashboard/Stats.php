<?php

namespace Aparlay\Core\Admin\Livewire\Dashboard;

use Aparlay\Core\Admin\Services\DashboardStatsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

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

        return view('default_view::livewire.dashboard.stats', compact('stats'));
    }

    private function getStats(): array
    {
        $start = $end = null;

        if (! empty($this->dateInterval) && ! $this->showAllDates) {
            $start = $this->startDate();
            $end = $this->endDate();
        }

        $statsService = new DashboardStatsService($start, $end);

        return $this->getDisplayedStats($statsService->getAnalyticStats());
    }

    private function getDisplayedStats(array $results): array
    {
        return [
            [
                'label' => 'Daily Active Users',
            ],
            [
                'label' => 'Monthly Active Users',
            ],
            [
                'label' => 'Unique Visitors',
            ],
            [
                'label' => 'Registrations',
            ],
            [
                'label' => 'New Videos',
                'value' => Arr::get($results, 'media_uploaded_videos'),
            ],
            [
                'label' => 'Verifications',
                'value' => Arr::get($results, 'user_verified'),
            ],
            [
                'label' => 'Subscriptions',
                'value' => Arr::get($results, 'payment_subscriptions'),
            ],
            [
                'label' => 'Renewals',
            ],
            [
                'label' => 'Tips',
                'value' => Arr::get($results, 'payment_tips'),
            ],
            [
                'label' => 'Total Billed (subs/rebill/tips)',
                'value' => (
                    Arr::get($results, 'payment_subscriptions_amount', 0) +
                    Arr::get($results, 'payment_tips_amount', 0)
                ),
            ],
            [
                'label' => 'Likes',
                'value' => Arr::get($results, 'media_likes'),
            ],
            [
                'label' => 'Comments',
                'value' => Arr::get($results, 'media_comments'),
            ],
            [
                'label' => 'Emails Sent',
                'value' => Arr::get($results, 'email_sent'),
            ],
            [
                'label' => 'Email Bounced',
            ],
            [
                'label' => 'Email Bounced Ratio',
            ],
        ];
    }
}
