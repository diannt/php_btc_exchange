<div class="wallets-block table-responsive">
    <?php if(count($data['dealsHistory']) > 0): ?>
    <table class="table table-hover" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th class="order_id">
                    <?php print Core::translateToCurrentLocale('ID'); ?>:
                </th>
                <th class="order_date">
                    <?php print Core::translateToCurrentLocale('Date'); ?>:
                </th>
                <th class="order_pair">
                    <?php print Core::translateToCurrentLocale('Pair'); ?>:
                </th>
                <th class="order_type">
                    <?php print Core::translateToCurrentLocale('Type'); ?>:
                </th>
                <th class="order_price">
                    <?php print Core::translateToCurrentLocale('Amount'); ?>:
                </th>
                <th class="order_amount">
                    <?php print Core::translateToCurrentLocale('Price'); ?>:
                </th>
                <th class="order_status">
                    <?php print Core::translateToCurrentLocale('Status'); ?>:
                </th>
                <th class="order_part">
                    <?php print Core::translateToCurrentLocale('Part'); ?>:
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data['dealsHistory'] as $value): ?>
        <?php if(count($value['deals']) > 0): ?>
          <tr class="order" onclick="showDeals(this);">
            <td class="greenMark" style="cursor: pointer;">
                <?php print $value['id']; ?>
                <b class="caret"></b>
            </td>
        <?php else: ?>
          <tr class="order">
            <td class="greenMark">
                <?php print $value['id']; ?>
            </td>
        <?php endif; ?>
            <td>
                <?php $a = explode(' ', $value['Date']);  $b = implode(' <br>' , $a); print $b; ?>
            </td>
            <td>
                <?php print $value['FirstCurrency'] . " / " . $value['SecondCurrency']; ?>
            </td>
            <td>
                <?php print Core::translateToCurrentLocale($value['Type']); ?>
            </td>
            <td>
                <?php print $value['Volume']; ?>
            </td>
            <td>
                <?php print $value['Price']; ?>
            </td>
            <td>
                <?php print Core::translateToCurrentLocale($value['Status']); ?>
            </td>
            <td>
                <?php print $value['Part']; ?>
            </td>
            <td>
            <?php if($value['Status'] == 'Active'): ?>
                <input class="btn" type="button" onclick="orders.CancelFromHistory('<?php print $value['id']; ?>');" value="<?php print Core::translateToCurrentLocale('Cancel'); ?>">
            <?php endif; ?>
            </td>
        </tr>
        <?php if(count($value['deals']) > 0): ?>
            <?php foreach($value['deals'] as $deal): ?>
                <tr class="deal" style="display: none; position:relative;">
                    <td></td>
                    <td colspan="3">
                        <span><?php print $deal['Date']; ?></span>
                    </td>
                    <td>
                        <span><?php print $deal['Volume']; ?></span>
                    </td>
                    <td>
                        <span><?php print $deal['Price']; ?></span>
                    </td>
                    <td colspan="2">
                        <span><?php print $deal['Status']; ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<script>
    function showDeals(e)
    {
        var itemBlock = $(e).next(".deal");
        $(itemBlock).toggle(function(){
            $(itemBlock).animate({height: 0});
        }, function(){
            $(itemBlock).animate({height: '35px'});
        });
    }
    $.fn.ready(function()
    {
        $(".wallets-block .table").tablesorter({dateFormat:"yyyy-mm-dd hh:mm:ss", headers: { 0: { sorter: false}, 1: {sorter: "shortDate"},  2: {sorter: false}, 3: {sorter: false}, 6: {sorter: false}, 7: {sorter: false}, 8: {sorter: false} } });
    });
</script>