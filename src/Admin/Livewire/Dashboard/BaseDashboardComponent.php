<?php

namespace Aparlay\Core\Admin\Livewire\Dashboard;

use Aparlay\Core\Admin\Livewire\Traits\HasDateRangeFilter;
use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class BaseDashboardComponent extends Component
{
    use HasDateRangeFilter;

    /**
     * @var string
     */
    public string $layout = self::LAYOUT_SIMPLE;

    protected string $view;

    const LAYOUT_SIMPLE = 'simple';
    const LAYOUT_TABLE = 'table';
    const LAYOUT_ADVANCED = 'advanced';
    const LAYOUT_FUNNEL = 'funnel';
    const LAYOUT_MESSAGE_STATS = 'message-stats';

    protected $listeners = [
        'layout-changed' => 'layoutChange',
        'dateInterval-changed' => 'dateIntervalChanged',
    ];

    /**
     * @param $newLayout
     * @return void
     */
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
