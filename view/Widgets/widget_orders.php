<?php
    if (isset($data['firstCurrency']))
        $currentRate = $data;
    else
        if (isset($_GET['Data']))
        {
            $currentRate = $_GET['Data'];
        }
    if (isset($currentRate['firstCurrency']))
        $orders = api::activeOrders($currentRate['firstCurrency'],$currentRate['secondCurrency']);
 ?>
<div class="title greenMark"><?php print Core::translateToCurrentLocale('Your current active orders'); ?>:</div>
<div class="widgetorders-list">
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
                                <span><?php print Core::translateToCurrentLocale('Amount'); ?>: <b><?php print $currentRate['firstCurrency'];?></b></span>
                                <span class="sortArrow">&nbsp;</span>
                            </div>
                        </th>
                        <th class="order_total">
                            <div class="th-inner">
                                <span><?php print Core::translateToCurrentLocale('Total'); ?>: <b><?php print $currentRate['secondCurrency']; ?></b></span>
                                <span class="sortArrow">&nbsp;</span>
                            </div>
                        </th>
                        <th class="order_action">
                            <div class="th-inner">
                                <span><?php print Core::translateToCurrentLocale('Action'); ?>:</span>
                                <span class="sortArrow">&nbsp;</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if(isset($orders['return'][0])): ?>
                    <?php foreach($orders['return'] as $value): ?>
                        <tr>
                            <td class="order_date">
                                <?php $a = explode(' ', $value['timestamp_created']);  $b = implode(' <br>' , $a); print $b; ?>
                            </td>
                            <td class="greenMark">
                                <?php print $value['type']; ?>
                            </td>
                            <td>
                                <?php print $value['amount']; ?>
                            </td>
                            <td>
                                <?php print $value['rate']; ?>
                            </td>
                            <td>
                                <?php print ($value['rate']*$value['amount']); ?>
                            </td>
                            <td>
                                <input type="button" onclick="orders.Cancel('<?php print $value['order_id']; ?>');" value="<?php print Core::translateToCurrentLocale('Cancel'); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="greenMark" style="text-align: left; padding-left: 31px;">
                            <img src="public/img/other/not.png">
                            <?php print Core::translateToCurrentLocale('No active orders at the moment'); ?>.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>