<li class="nav-item form-inline">
    <label for="globalSearch"><i class="fa fa-search"></i></label>

    <div class="form-inline sidebar-search-open">

        <div class="input-group">
            <input class="form-control form-control-sidebar mx-2" id="globalSearch" type="search"
                   placeholder="Search anything"
                   wire:model.debounce.300ms="searchQuery">
        </div>
        <div class="sidebar-search-results d-none" id="searchResults">
            <div class="list-group" style="background: white">
                @foreach($results as $result)
                    <a href="{{$result['link']}}" class="list-group-item">
                        <div class="search-title">{{$result['title']}}</div>
                        <div class="search-path"> {{$result['category']}} </div>
                    </a>
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
</li>
