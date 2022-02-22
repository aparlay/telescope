const dataTypeFields = {
    0 : `<input type="text" id="value" name="value" class="string valueType form-control">`,
    1 : `<div class="custom-control custom-switch bool valueType">
            <input type="checkbox" name="value" class="name custom-control-input" id="customSwitches">
            <label class="custom-control-label" for="customSwitches"></label>
        </div>`,
    2 : `<input type="number" id="value" name="value" class="int valueType form-control">`,
    3 : `<div class="input-group datetime valueType" id="valuedatetime" data-target-input="nearest">
                    <input type="text" name="value" class="form-control datetimepicker-input" data-target="#valuedatetime"/>
                    <div class="input-group-append" data-target="#valuedatetime" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>`,
    4 : `<textarea name="value" id="value" rows="10" class="json valueType form-control"
              placeholder="{'key': 'value'} | string"></textarea>`
};

$(document).ready(function() {
    $('#group').select2({
        tags: true
    })

    $(document).on('change', '#type', function() {
        $('.valueType').remove();
        let currVal = parseInt($(this).val());
        $('#fieldContainer').append(dataTypeFields[currVal]);

        if(currVal === 3) {
            $('#valuedatetime').datetimepicker({
                icons: { time: 'far fa-clock' } ,
                sideBySide: true,
                format: 'YYYY-MM-DD HH:mm:ss'
            });
        }
    })
});

