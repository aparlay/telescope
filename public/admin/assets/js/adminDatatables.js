$(document).ready(function() {
    $('#datatables thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#datatables thead');

    $('#datatables').DataTable( {
        orderCellsTop: true,
        processing : true,
        serverSide : true,
        pageLength : 20,
        responsive: true,
        lengthChange: false,
        dom : 'rtip',
        orderMulti: false,
        autoWidth: false,
        ajax : '/ajax/user',
        order: [[ 8, "desc" ]],
        columns: [
            {data: 'username'},
            {data: 'email'},
            {data: 'full_name', orderable: false},
            {data: 'status', render: function(data, display, response) {
                    return response.status_badge.status + '<br>' + response.status_badge.is_verified + '<br>' + response.status_badge.gender;
                }},
            {data: 'visibility'},
            {data: 'follower_count', orderable: false},
            {data: 'like_count', orderable: false},
            {data: 'media_count', orderable: false},
            {data: 'created_at', render: function (response) {
                    var datetime = new Date(response);
                    return [datetime.getFullYear(),
                            datetime.getMonth()+1,
                            datetime.getDate(),
                        ].join('-')+' '+
                        [datetime.getHours(),
                            datetime.getMinutes(),
                            datetime.getSeconds()].join(':');
                } },
            {data: null, render: function(response) {
                    return '<a class="btn btn-primary btn-sm" href="/user/'+ response._id +'" title="View"><i class="fas fa-eye"></i> View</a>'
                }, orderable: false},

        ],
        initComplete: function () {
            var api = this.api();
            var filterable = ['username', 'email', 'status', 'visibility']
            // For each column
            api.columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();

                    if($.inArray(title.toLowerCase(), filterable) !== -1) {
                        if(title === 'Status') {
                            var html = '<select class="form-control" name="" id="">' + userStatus
                            '</select>';
                            $(cell).html(html);
                        }else if(title === 'Visibility') {
                            var html = '<select class="form-control" name="" id="">' + userVisibility
                            '</select>';
                            $(cell).html(html);
                        } else {
                            $(cell).html('<input type="text" class="form-control" placeholder="' + title + '" />');
                        }

                        // On every keypress in this input
                        $('input, select', $('.filters th').eq($(api.column(colIdx).header()).index())).off('keyup change')
                            .on('keyup change', function (e) {
                                e.stopPropagation();
                                // Get the search value
                                $(this).attr('title', $(this).val());

                                var cursorPosition = this.selectionStart;

                                if(!isNaN(this.value) || this.value.length > 2) {
                                    // Search the column for that value
                                    api.column(colIdx).search(this.value).draw();
                                }
                            });
                    } else {
                        $(cell).html('');
                    }
                });
        }
    });
})
