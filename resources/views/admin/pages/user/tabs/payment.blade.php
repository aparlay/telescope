<div class="tab-pane" id="payment">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <h4>Credit Cards</h4>
                <div class="col-12 table-responsive">
                    @php
                        $heads = [
                            'Username',
                            'Status',
                            '',
                            'Expiration Month',
                            'Expiration Year',
                            'Created at',
                            '',
                            ''
                        ];

                        $config = [
                            'processing' => true,
                            'serverSide' => true,
                            'pageLength' => config('core.admin.lists.user_page_count'),
                            'responsive' => true,
                            'lengthChange' => false,
                            'dom' => 'rtip',
                            'orderMulti' => false,
                            'bInfo' => false,
                            'searchCols' => [['search' => $user->username]],
                            'autoWidth' => false,
                            'ajax' => route('payment.admin.ajax.credit-card.index'),
                            'order' => [[5, 'desc']],
                            'columns' => [
                                ['data' => 'creator.username'],
                                ['data' => 'status_button','orderData' => 2, 'target' => 2],
                                ['data' => 'status','visible' => false],
                                ['data' => 'expire_month', 'orderable' => false],
                                ['data' => 'expire_year', 'orderable' => false],
                                ['data' => 'formatted_created_at','orderData' => 6, 'target' => 6],
                                ['data' => 'created_at','visible' => false],
                                ['data' => 'view_button', 'orderable' => false],
                            ],
                        ]
                    @endphp
                    <x-adminlte-datatable id="creditCardsDatatable" :heads="$heads" :config="$config">
                    </x-adminlte-datatable>
                </div>


                <h4>Earned Tips</h4>
                <div class="col-12 table-responsive">
                    @php
                        $heads = [
                            'User',
                            'Creator',
                            '',
                            'Media',
                            'Currency',
                            'Amount',
                            'Status',
                            '',
                            'Created at',
                            '',
                            ''
                        ];

                    $config = [
                        'processing' => true,
                        'serverSide' => true,
                        'pageLength' => config('core.admin.lists.user_page_count'),
                        'responsive' => true,
                        'lengthChange' => false,
                        'dom' => 'rtip',
                        'orderMulti' => false,
                        'autoWidth' => false,
                        'bInfo' => false,
                        'searchCols' => [['search' => $user->username]],
                        'ajax' => route('payment.admin.ajax.tip.index'),
                        'order' => [[8, 'desc']],
                        'columns' => [
                            ['data' => 'user.username'],
                            ['data' => 'link_to_creator', 'orderData' => 2, 'target' => 2],
                            ['data' => 'creator.username','visible' => false],
                            ['data' => 'link_to_media', 'orderable' => false],
                            ['data' => 'currency'],
                            ['data' => 'amount'],
                            ['data' => 'status_button','orderData' => 7, 'target' => 7],
                            ['data' => 'status','visible' => false],
                            ['data' => 'formatted_created_at','orderData' => 9, 'target' => 9],
                            ['data' => 'created_at','visible' => false],
                            ['data' => 'view_button', 'orderable' => false],
                        ],
                    ]

                    @endphp
                    <x-adminlte-datatable id="earnedTipsDatatable" :heads="$heads" :config="$config">
                    </x-adminlte-datatable>
                </div>

                <h4>Send Tips</h4>
                <div class="col-12 table-responsive">
                    @php
                        $heads = [
                            'User',
                            '',
                            'Creator',
                            'Media',
                            'Currency',
                            'Amount',
                            'Status',
                            '',
                            'Created at',
                            '',
                            ''
                        ];

                    $config = [
                        'processing' => true,
                        'serverSide' => true,
                        'pageLength' => config('core.admin.lists.user_page_count'),
                        'responsive' => true,
                        'lengthChange' => false,
                        'dom' => 'rtip',
                        'orderMulti' => false,
                        'autoWidth' => false,
                        'bInfo' => false,
                        'searchCols' => ['','',['search' => $user->username]],
                        'ajax' => route('payment.admin.ajax.tip.index'),
                        'order' => [[8, 'desc']],
                        'columns' => [
                            ['data' => 'link_to_user', 'orderData' => 1, 'target' => 1],
                            ['data' => 'user.username','visible' => false],
                            ['data' => 'creator.username'],
                            ['data' => 'link_to_media', 'orderable' => false],
                            ['data' => 'currency'],
                            ['data' => 'amount'],
                            ['data' => 'status_button','orderData' => 7, 'target' => 7],
                            ['data' => 'status','visible' => false],
                            ['data' => 'formatted_created_at','orderData' => 9, 'target' => 9],
                            ['data' => 'created_at','visible' => false],
                            ['data' => 'view_button', 'orderable' => false],
                        ],
                    ]
                    @endphp
                    <x-adminlte-datatable id="sendTipsDatatable" :heads="$heads" :config="$config">
                    </x-adminlte-datatable>
                </div>


                <h4>Subscriptions</h4>
                <div class="col-12 table-responsive">
                    @php
                        $heads = [
                            '',
                            'User',
                            '',
                            'Status',
                            '',
                            'Created at',
                            '',
                            ''
                        ];

                    $config = [
                        'processing' => true,
                        'serverSide' => true,
                        'pageLength' => config('core.admin.lists.user_page_count'),
                        'responsive' => true,
                        'lengthChange' => false,
                        'bInfo' => false,
                        'dom' => 'rtip',
                        'orderMulti' => false,
                        'searchCols' => [['search' => $user->username]],
                        'autoWidth' => false,
                        'ajax' => route('payment.admin.ajax.subscription.index'),
                        'order' => [[5, 'desc']],
                        'columns' => [
                            ['data' => 'creator.username','visible' => false],
                            ['data' => 'link_to_user', 'orderData' => 2, 'target' => 2],
                            ['data' => 'user.username','visible' => false],
                            ['data' => 'status_button','orderData' => 4, 'target' => 4],
                            ['data' => 'status','visible' => false],
                            ['data' => 'formatted_created_at','orderData' => 6, 'target' => 6],
                            ['data' => 'created_at','visible' => false],
                            ['data' => 'view_button', 'orderable' => false]
                        ],
                    ]
                    @endphp
                    <x-adminlte-datatable id="subscriptionsDatatable" :heads="$heads" :config="$config">
                    </x-adminlte-datatable>
                </div>

                <h4>Subscribers</h4>
                <div class="col-12 table-responsive">
                    @php
                        $heads = [
                            '',
                            'Creator',
                            '',
                            'Status',
                            '',
                            'Created at',
                            '',
                            ''
                        ];

                    $config = [
                        'processing' => true,
                        'serverSide' => true,
                        'pageLength' => config('core.admin.lists.user_page_count'),
                        'responsive' => true,
                        'lengthChange' => false,
                        'bInfo' => false,
                        'dom' => 'rtip',
                        'orderMulti' => false,
                        'searchCols' => ['','',['search' => $user->username]],
                        'autoWidth' => false,
                        'ajax' => route('payment.admin.ajax.subscription.index'),
                        'order' => [[5, 'desc']],
                        'columns' => [
                            ['data' => 'creator.username','visible' => false],
                            ['data' => 'link_to_creator', 'orderData' => 0, 'target' => 0],
                            ['data' => 'user.username','visible' => false],
                            ['data' => 'status_button','orderData' => 4, 'target' => 4],
                            ['data' => 'status','visible' => false],
                            ['data' => 'formatted_created_at','orderData' => 6, 'target' => 6],
                            ['data' => 'created_at','visible' => false],
                            ['data' => 'view_button', 'orderable' => false]
                        ],
                    ]
                    @endphp
                    <x-adminlte-datatable id="subscribersDatatable" :heads="$heads" :config="$config">
                    </x-adminlte-datatable>
                </div>
            </div>
        </div>
    </div>
</div><?php<?php
