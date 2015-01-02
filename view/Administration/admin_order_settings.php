<div class="wallets-block table-responsive">
    <?php if(count($data['currencies']) > 0): ?>
        <table class="table table-bordered">
            <tr>
                <td>
                    <span class="greenMark"><?php print Core::translateToCurrentLocale('Name'); ?></span>
                </td>
                <td>
                    <span class="greenMark"><?php print Core::translateToCurrentLocale('Full name'); ?></span>
                </td>
                <td>
                    <span class="greenMark"><?php print Core::translateToCurrentLocale('Min order amount'); ?></span>
                </td>
            </tr>
            <?php foreach($data['currencies'] as $value): ?>
                <tr id='<?php print $value['id']; ?>'>
                    <td>
                        <span class="greenMark"><?php print $value['Name']; ?></span>
                    </td>
                    <td>
                        <span class="greenMark"><?php print $value['tradeName']; ?></span>
                    </td>
                    <td name="editable">
                        <span class="greenMark" name="min_order_amount"><?php print $value['min_order_amount']; ?></span>
                    </td>
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
            url:'/admin/update_order_settings',
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