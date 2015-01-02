<div class="wallets-block table-responsive">
    <?php if(count($data['io_fees']) > 0): ?>
        <table class="table table-bordered">
            <tr>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Currency'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Payment system'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('System fee'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Input fee'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Input min'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Input max'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Output fee'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Output min'); ?></span></td>
                <td><span class="greenMark"><?php print Core::translateToCurrentLocale('Output max'); ?></span></td>
            </tr>
            <?php foreach($data['io_fees'] as $value): ?>
                <tr id='<?php print $value['id']; ?>'>
                    <td><span class="greenMark"><?php print $value['currencyName']; ?></span></td>
                    <td><span class="greenMark"><?php print $value['trade_name']; ?></span></td>
                    <td><span class="greenMark" style="color: red"><?php print $value['system_fee']; ?></span></td>
                    <td name="editable"><span class="greenMark" name="input_fee"><?php print $value['input_fee']; ?></span></td>
                    <td name="editable"><span class="greenMark" name="input_min"><?php print $value['input_min']; ?></span></td>
                    <td name="editable"><span class="greenMark" name="input_max"><?php print $value['input_max']; ?></span></td>
                    <td name="editable"><span class="greenMark" name="output_fee"><?php print $value['output_fee']; ?></span></td>
                    <td name="editable"><span class="greenMark" name="output_min"><?php print $value['output_min']; ?></span></td>
                    <td name="editable"><span class="greenMark" name="output_max"><?php print $value['output_max']; ?></span></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<script>
    $.fn.ready(function() {
        $('table').on('click', 'td', function() {
            if ($(this).attr('name') != 'editable'){
                return;
            }
            if ($(this).find('input').length) {
                return;
            }

            var $e = $(this).find('span');
            var val = $e.html();
            $(this).html('<input type="text" id="edit" name="' + $e.attr('name') + '" value="' + val + '">');
            var $newE = $(this).find('input');
            $newE.focus();

            $newE.on('blur', onEditBlur);
        });
    });

    function onEditBlur()
    {
        var rowId = $(this).parent().parent().attr('id');
        var fieldName = $(this).attr('name');
        var value = $(this).val();
        var $td = $(this).parent();
        $td.html('<span class="greenMark" name="' + fieldName + '">' + value + '</span>');

        $.ajax({
            type:'post',
            url:'/admin/update_io_fee',
            data:{
                id: rowId,
                fieldName: fieldName,
                value: value
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == 0){
                    alert(data.error);
                    $td.html('<input type="text" id="edit" name="' + fieldName + '" value="' + value + '">');
                    var $newE = $td.find('input');
                    $newE.focus();
                    $newE.on('blur', onEditBlur);
                }
            }
        });
    }

    $(window).keydown(function(event) {
        if(event.keyCode == 13) {
            $('#edit').blur();
        }
    });
</script>

