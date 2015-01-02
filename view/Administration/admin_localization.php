<div class="wallets-block">
    <?php if(count($data['localizationList']) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover" id='data'>
                <tr>
                    <form class="form-horizontal" role="form" method="post" action="/admin/addLocalizationRecord">
                        <?php foreach($data['languages'] as $lang): ?>
                            <td>
                                <input id="<?php print $lang; ?>" type="text" name="<?php print $lang; ?>" placeholder="<?php print $lang; ?>"">
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <input id="submit" type="submit" value="<?php print Core::translateToCurrentLocale('Add'); ?>">
                        </td>
                    </form>
                </tr>
                <?php foreach($data['localizationList'] as $value): ?>
                    <tr id='<?php print $value['id']; ?>'>
                        <?php foreach($data['languages'] as $lang): ?>
                            <td>
                                <span class="greenMark" name="<?php print $lang; ?>">
                                    <?php print urldecode($value[$lang]); ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
<script>
    $.fn.ready(function() {
        $('table').on('click', 'span', function() {
            var $e = $(this).parent();
            var val = $(this).html();
            $e.html('<input type="text" id="edit" name="' + $(this).attr('name') + '" value="' + val + '">');
            var $newE = $e.find('input');
            $newE.focus();

            $newE.on('blur', function() {
                var rowId = $(this).parent().parent().attr('id');
                var language = $(this).attr('name');
                var value = $(this).val();
                $(this).parent().html('<span class="greenMark" name="' + language + '">' + value + '</span>');

                $.ajax({
                    type:'post',
                    url:'/admin/updateLocalizationRow',
                    data:{
                        'id': rowId,
                        'lang': language,
                        'phrase': value
                    }
                });
            });
        });
    });

    $(window).keydown(function(event) {
        if(event.keyCode == 13) {
            $('#edit').blur();
        }
    });
</script>