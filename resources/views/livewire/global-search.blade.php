<div class="nav-item form-inline flex-fill mx-2">
    <label for="globalSearch"></label>

    <div class="form-inline sidebar-search-open flex-fill">

        <div class="input-group flex-fill">
            <input class="form-control form-control-sidebar flex-fill" id="globalSearch" type="search"
                   placeholder="Search anything"
                   wire:model.debounce.300ms="searchQuery">
        </div>
        <div class="sidebar-search-results global-search d-none" id="searchResults">
            <div class="list-group bg-white border-left border-right border-bottom">
                @foreach($results as $category => $models)
                    @if(count($models) > 0)
                        <div class="float-left p-1 pl-2 text-bold text-gray-800">
                            {{$category}}
                        </div>
                    @endif
                    @foreach($models as $model)
                        @if($category == 'User')
                            <x-username-avatar :user="$model" :class="'pl-3 py-1 result-item'"/>
                        @elseif($category == 'Payout')
                            <x-payout-search-result :payout="$model"/>
                        @elseif($category == 'Order')
                            <x-order-search-result :order="$model"/>
                        @elseif($category == 'Chat')
                            <x-chat-search-result :chat="$model"/>
                        @elseif($category == 'Media')
                            <x-media-search-result :media="$model"/>
                        @endif
                    @endforeach
                @endforeach

                @if(count($results) == 0 && strlen($searchQuery) > 3)
                    <a href="#" class="list-group-item">
                        <div class="search-title">No results</div>
                        <div class="search-path"></div>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('load', function () {
            let results = $("#searchResults");

            window.addEventListener('search-results-updated', function () {
                results.removeClass('d-none');
            });

            $(window).click((e) => {
                if (e.target.id !== 'globalSearch') {
                    results.addClass('d-none');
                } else {
                    results.removeClass('d-none');
                }
            })
        });
    </script>
</div>