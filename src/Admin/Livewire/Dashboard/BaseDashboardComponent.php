<?php

namespace Aparlay\Core\Admin\Livewire\Dashboard;

use Aparlay\Core\Admin\Livewire\Traits\HasDateRangeFilter;
use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class BaseDashboardComponent extends Component
{
    use HasDateRangeFilter;
    public const LAYOUT_SIMPLE        = 'simple';
    public const LAYOUT_TABLE         = 'table';
    public const LAYOUT_ADVANCED      = 'advanced';
    public const LAYOUT_FUNNEL        = 'funnel';
    public const LAYOUT_MESSAGE_STATS = 'message-stats';
    public string $layout             = self::LAYOUT_SIMPLE;
    protected string $view;
    protected $listeners              = [
        'layout-changed' => 'layoutChange',
        'dateInterval-changed' => 'dateIntervalChanged',
    ];

    public function layoutChange($newLayout): void
    {
        $this->layout = $newLayout;
    }

    public function render(): View
    {
        return view(
            sprintf('default_view::livewire.dashboard.%s', $this->view)
        );
    }
}
