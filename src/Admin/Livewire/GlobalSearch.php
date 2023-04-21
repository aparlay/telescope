<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Services\GlobalSearchService;
use Livewire\Component;

final class GlobalSearch extends Component
{
    public string $searchQuery = '';
    public array $results      = [];

    public function render()
    {
        $this->refreshResults();

        return view('default_view::livewire.global-search');
    }

    private function refreshResults()
    {
        if (strlen($this->searchQuery)) {
            $this->results = GlobalSearchService::search($this->searchQuery);
            $this->dispatchBrowserEvent('search-results-updated');
        }
    }
}
