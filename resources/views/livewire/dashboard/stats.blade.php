<section class="container-fluid">
    <div class="row">
        @foreach($stats as $stat)
            <div class="my-2 {{$cardClass}}">
                <div class="m-1 card h-100 rounded-lg border border-gray-200 shadow-md">
                    <div class="card-body align-middle p-0 text-center">
                        @if(isset($stat['value']))
                            <div class="text text-gray h2">
                                {{ $stat['value'] }}
                            </div>
                        @else
                            <div class="text-gray h2">
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
