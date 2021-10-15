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
        table.columns().search( '' ).draw();
    })
})
