<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Services\GlobalSearchService;
use Livewire\Component;

final class SearchBar extends Component
{
    public string $searchQuery = '';

    public array $results = [];

    public function render()
    {
        $this->results = GlobalSearchService::search($this->searchQuery);

        $this->dispatchBrowserEvent('search-results-updated');

        return view('default_view::livewire.search-bar');
    }
}
