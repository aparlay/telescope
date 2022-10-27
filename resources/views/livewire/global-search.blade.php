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
                @foreach($results as $category)
                    @if($result['category'] == 'User')
                        <a href="#" class="list-group-item">
                            <div class="search-title">No results</div>
                            <div class="search-path"></div>
                        </a>
                    @endif
                    <a href="{{$result['link']}}" class="list-group-item">
                        <div class="search-title">{{$result['title']}}</div>
                        <div class="search-path"> {{$result['category']}} </div>
                    </a>
                    <li class="select2-results__option" role="group" aria-label="<i class=&quot;fa fa-user&quot;></i> Users" data-select2-id="15">
                        <strong class="select2-results__group"><b><i class="fa fa-user"></i> Users</b></strong>
                        <ul class="select2-results__options select2-results__options--nested">
                            @foreach($category as $item)
                            <li class="select2-results__option select2-results__option--highlighted" role="option" aria-selected="false" data-select2-id="9">
                                <img src="https://production-media1.alua.com/59ca4c8ac3b8c5002c3901cc/1d7a10df72f76ca093631ec7ef1d378c79860db7053d35debee0ad4f10c9fb7e" alt="" style="width:20px;height:20px">
                                [U] tes [Tayla Stafford]
                            </li>
                            @endforeach

                        </ul>
                    </li>
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
