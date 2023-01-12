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
                'value' => Arr::get($results, 'returned_users'),
            ],
            [
                'label' => 'Active Users',
                'value' => Arr::get($results, 'active_users'),
            ],
            [
                'label' => 'Registrations Atm.',
                'value' => Arr::get($results, 'user_registered_attempt'),
            ],
            [
                'label' => 'Registrations Act.',
                'value' => Arr::get($results, 'user_registered_active'),
            ],
            [
                'label' => 'Verifications',
                'value' => Arr::get($results, 'user_id_verified'),
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
                'label' => 'Video Played',
                'value' => Arr::get($results, 'user_watched'),
            ],
            [
                'label' => 'Video Watched',
                'value' => round(\Carbon\CarbonInterval::seconds(Arr::get($results, 'user_duration'))->cascade()->totalHours).'H.',
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
                'value' => Arr::get($results, 'email_sent') + Arr::get($results, 'email_delivered') + Arr::get($results, 'email_bounced') + Arr::get($results, 'email_deferred'),
            ],
            [
                'label' => 'Email Delivered',
                'value' => Arr::get($results, 'email_delivered'),
            ],
            [
                'label' => 'Email Bounced',
                'value' => Arr::get($results, 'email_bounced'),
            ],
            [
                'label' => 'Email Deferred',
                'value' => Arr::get($results, 'email_deferred'),
            ],
        ];
    }
}
