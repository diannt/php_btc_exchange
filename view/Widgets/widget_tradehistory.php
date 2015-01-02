<?php
    if (isset($data['firstCurrency']))
        $currentRate = $data;
    else
        if (isset($_GET['Data']))
        {
            $currentRate = $_GET['Data'];
        }
    if (isset($currentRate['firstCurrency']))
    {
        if (isset($currentRate['count']))
            $tradeHistory = api::tradeHistory($currentRate['firstCurrency'], $currentRate['secondCurrency'], $currentRate['count']);
        else
            $tradeHistory = api::tradeHistory($currentRate['firstCurrency'], $currentRate['secondCurrency']);
    }
    $volume=0;
    $price=0;
?>
<div class="title greenMark"><?php print Core::translateToCurrentLocale('Trade history'); ?>:</div>
<div class="widgettradehistory-table">
    <div class="fixed-table-container">
        <div class="table-responsive">
            <table class="table table-hover" cellspacing="0">
                <thead>
                <tr>
                    <th class="order_date">
                        <div class="th-inner">
                            <span><?php print Core::translateToCurrentLocale('Date'); ?>:</span>
                            <span class="sortArrow">&nbsp;</span>
                        </div>
                    </th>
                    <th class="order_type">
                        <div class="th-inner">
                            <span><?php print Core::translateToCurrentLocale('Type'); ?>:</span>
                            <span class="sortArrow">&nbsp;</span>
                        </div>
                    </th>
                    <th class="order_price">
                        <div class="th-inner">
                            <span><?php print Core::translateToCurrentLocale('Price'); ?>:</span>
                            <span class="sortArrow">&nbsp;</span>
                        </div>
                    </th>
                    <th class="order_amount">
                        <div class="th-inner">
                            <span><?php print Core::translateToCurrentLocale('Amount'); ?>: <b><?php print $currentRate['firstCurrency']; ?></b></span>
                            <span class="sortArrow">&nbsp;</span>
                        </div>
                    </th>
                    <th class="order_hidden">
                        &nbsp;
                    </th>
                    <th class="order_total">
                        <div class="th-inner">
                            <span><?php print Core::translateToCurrentLocale('Total'); ?>: <b><?php print $currentRate['secondCurrency']; ?></b></span>
                            <span class="sortArrow">&nbsp;</span>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if(isset($tradeHistory['return'])): ?>
                    <?php foreach($tradeHistory['return'] as $value): ?>
                        <tr>
                            <td class="order_date">
                                <span title="<?php print date('Y-m-d H:i:s', $value['timestamp']); ?>"><?php $a = explode(' ', date('Y-m-d H:i:s', $value['timestamp']));  $b = implode(' <br>' , $a); print $b; ?></span>
                            </td>
                            <td>
                                <b class="greenMark"><?php print $value['type']; ?></b>
                            </td>
                            <td>
                                <?php print $value['rate'];?> <?php print $currentRate['secondCurrency']; ?>
                            </td>
                            <td>
                                <?php print $value['amount']; ?> <?php print $currentRate['firstCurrency']; ?>
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                <?php print ($value['amount']*$value['rate']); ?> <?php print $currentRate['secondCurrency'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <?php print Core::translateToCurrentLocale('No trades'); ?>.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>