<div class="tab-pane" id="medias">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                @php
                    $heads = [
                        'Cover',
                        'Created By',
                        'Description',
                        '',
                        'Status',
                        'Likes',
                        'Visits',
                        'Sort Score',
                        'Created At',
                        '',
                        ''
                    ];
                $config = [
                    'processing' => true,
                    'serverSide' => true,
                    'pageLength' => config('core.admin.lists.page_count'),
                    'responsive' => true,
                    'lengthChange' => false,
                    'dom' => 'rtip',
                    'orderMulti' => false,
                    'autoWidth' => false,
                    'ajax' => route('core.admin.ajax.media.index'),
                    'order' => [[8, 'desc']],
                    'searching' => true,
                    'searchCols' => [null, ['search' => $user->username]],
                    'bInfo' => false,
                    'columns' => [
                        ['data' => 'file', 'orderable' => false],
                        ['data' => 'creator.username', 'orderable' => false],
                        ['data' => 'description', 'orderable' => false],
                        ['data' => 'status', 'visible' => false],
                        ['data' => 'status_badge', 'orderData' => 3, 'target' => 3],
                        ['data' => 'like_count', 'orderable' => false],
                        ['data' => 'visit_count', 'orderable' => false],
                        ['data' => 'sort_score', 'orderable' => false],
                        ['data' => 'date_formatted','orderData' => 9, 'target' => 9],
                        ['data' => 'created_at','visible' => false],
                        ['data' => 'action', 'orderable' => false],
                    ],
                ];
                @endphp
                <x-adminlte-datatable id="mediaDatatable" :heads="$heads" :config="$config">
                </x-adminlte-datatable>
            </div>
        </div>
    </div>
</div>