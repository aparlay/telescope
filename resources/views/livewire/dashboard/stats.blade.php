<section class="container-fluid">
    <div class="row {{$showAllDates}}">
        @foreach($stats as $stat)
            <div @class(['my-2', 'col-6', 'col-md-2' => (count($stats) > 8), 'col-md-3' => (count($stats) <= 8)])>
                <div class="m-1 card h-100 rounded-lg border border-gray-200 shadow-md">
                    <div class="card-body align-middle p-0 text-center">
                        @if(isset($stat['value']))
                            <div class="text text-gray-dark h2 mb-0 mt-1">
                                {{ $stat['value'] }}
                            </div>
                        @else
                            <div class="text-gray h2 mb-0 mt-1">
                                ({{__('not set')}})
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center font-italic bg-white text-gray align-bottom">
                        @if(isset($stat['link']))
                            <a href="{{$stat['link']}}">{{__($stat['label'])}}</a>
                        @else
                            <div>{{$stat['label']}}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
