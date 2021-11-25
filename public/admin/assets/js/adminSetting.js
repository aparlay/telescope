$(document).ready(function() {
    $('#group').select2({
        tags: true
    })

    $('#type').change(function() {
        let typeValue = $(this).val();

        if(typeValue === 'string') {
            $('textarea#value').hide();
            $('input#value').show();
        } else {
            $('textarea#value').show();
            $('input#value').hide();
        }
    })
})
