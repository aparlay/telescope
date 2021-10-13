@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('title', 'Media')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/media.css') }}"/>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @include('default_view::admin.parts.breadcrumbs')

    <table id="mediaList" class="table table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <th scope="col">Cover</th>
            <th scope="col">Created By</th>
            <th scope="col">Description</th>
            <th scope="col">Status</th>
            <th scope="col">Created At</th>
            <th scope="col">Likes</th>
            <th scope="col">Visits</th>
            <th scope="col">Sort Score</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($media_list as $media)
            <tr>
                <td><img src="{{ $media->file }}"/></td>
                <td>
                    <img class="table-avatar mr-1.5" src="{{ $media->creator['avatar'] }}"
                         alt="{{ $media->creator['username'] }}"/>
                    {{ $media->creator['username'] }}
                </td>
                <td>{{ $media->description }}</td>
                <td><span class="badge bg-{{ $media->status_text['color'] }}">{{ $media->status_text['text'] }}</span>
                </td>
                <td>{{ $media->created_at }}</td>
                <td>{{ $media->like_count }}</td>
                <td>{{ $media->visit_count }}</td>
                <td>{{ $media->sort_score }}</td>
                <td>
                    <a class="btn btn-primary btn-sm" href="/media/{{ $media->id }}" title="View"><i
                            class="fas fa-eye"></i> View</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
@section('js')
    <script>
        var table;

        $(document).ready(function () {

            $('#mediaList thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#mediaList thead');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            table = $('#mediaList').DataTable({
                    "orderMulti": false,
                    "bPaginate": true,
                    "processing": true,
                    "serverSide": true,
                    "orderCellsTop": true,
                    "autoFill": true,
                    "iDisplayLength": {{ config('core.admin.lists.page_count') ? config('core.admin.lists.page_count') : 20 }},
                    "ajax": {
                        "url": "media/list",
                        "deferRender": true,
                        "type": "POST"
                    },
                    "columns": [
                        {data: "file", name: "file", orderable: false},
                        {data: "creator_username", name: "creator.username", orderable: true},
                        {data: "description", name: "description", orderable: false},
                        {data: "status", name: "status", orderable: true},
                        {data: "created_at", name: "created_at", orderable: true},
                        {data: "like_count", name: "like_count", orderable: false},
                        {data: "visit_count", name: "visit_count", orderable: false},
                        {data: "sort_score", name: "sort_score", orderable: false},
                        {data: "detail_url", name: "detail_url", orderable: false},
                    ],
                    initComplete: function () {
                        var api = this.api();
                        var filterable = ['created by', 'status']
                        // For each column
                        api.columns()
                            .eq(0)
                            .each(function (colIdx) {
                                // Set the header cell to contain the input element
                                var cell = $('.filters th').eq(
                                    $(api.column(colIdx).header()).index()
                                );
                                var title = $(cell).text();

                                if ($.inArray(title.toLowerCase(), filterable) !== -1) {
                                    console.log(title)
                                    $(cell).html('<input type="text" placeholder="' + title + '" />');

                                    // On every keypress in this input
                                    $('input', $('.filters th').eq($(api.column(colIdx).header()).index())).off('keyup change')
                                        .on('keyup change', function (e) {
                                            e.stopPropagation();

                                            // Get the search value
                                            $(this).attr('title', $(this).val());
                                            var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                            var cursorPosition = this.selectionStart;
                                            // Search the column for that value
                                            api.column(colIdx)
                                                .search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '',
                                                    this.value != '', this.value == ''
                                                ).draw();

                                            $(this).focus()[0]
                                                .setSelectionRange(cursorPosition, cursorPosition);
                                        });
                                } else {
                                    $(cell).html('');
                                }
                            }
                        );
                    }
                },
            );

        });
    </script>
@endsection
