$(document).ready(function() {
    let table = $('#datatables').DataTable()
    $('#submitFilter').click(function() {
        let fieldFilters = {};
        $('#filters input, #filters select').each(function() {
            fieldFilters[$(this).attr('name')] = $(this).val();
            let colPosition = $(this).data('column');
            table.column(colPosition).search($(this).val())
        })
        table.draw();
    });

    $('#clearFilter').click(function() {
        $('#filters')[0].reset();
        $('input:hidden').val('');
        $('#date-preview').empty();
        if(window.location.pathname === '/media/moderation') {
            let search = $('#default-search');
            let col = $(search).data('searchcol');
            let value = $(search).data('search');

            table.columns().search( '' ).columns(col).search(value).draw();
        } else {
            table.columns().search( '' ).draw();
        }
    })

    $('#daterange-btn').daterangepicker(
        {
            ranges   : {
                'Today'       : [moment(), moment()],
                'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment()
        },
        function (start, end) {
            $('#date-preview').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
        }
    )
})
