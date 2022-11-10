<?php

namespace Aparlay\Core\Admin\Livewire\Dashboard;

use Aparlay\Core\Admin\Services\DashboardStatsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

final class Stats extends BaseDashboardComponent
{
    public array $stats = [];

    public function mount()
    {
        $today = now()->format('Y-m-d');

        $this->dateInterval = [
            $today, $today,
        ];
    }

    public function render(): View
    {
        $this->stats = $this->getStats();

        return view('default_view::livewire.dashboard.stats');
    }

    private function getStats(): array
    {
        if ($this->showAllDates) {
            $start = $end = null;
        } else {
            if (! empty($this->dateInterval)) {
                $start = $this->startDate();
                $end = $this->endDate();
            } else {
                $start = $end = today();
            }
        }

        $statsService = new DashboardStatsService();

        return $this->getDisplayedStats($statsService->getAnalyticStats($start, $end));
    }

    private function getDisplayedStats(array $results): array
    {
        return [
            [
                'label' => 'Unique Visitors',
                'value' => Arr::get($results, 'google_analytics_new_users'),
            ],
            [
                'label' => 'Active Users',
                'value' => Arr::get($results, 'google_analytics_active_users'),
            ],
            [
                'label' => 'Registrations',
                'value' => Arr::get($results, 'user_registered'),
            ],
            [
                'label' => 'Verifications',
            ],
            [
                'label' => 'Video Uploads',
                'value' => Arr::get($results, 'media_uploaded'),
            ],
            [
                'label' => 'Video Approvals',
                'value' => Arr::get($results, 'media_confirmed'),
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
                'label' => 'Emails Sent',
                'value' => Arr::get($results, 'email_sent'),
            ],
            [
                'label' => 'Email Verifications',
                'value' => (Arr::get($results, 'email_sent') > 1) ? Arr::get($results, 'user_verified').'['.round(100 * Arr::get($results, 'user_verified') / Arr::get($results, 'email_sent')).'%]' : null,
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
