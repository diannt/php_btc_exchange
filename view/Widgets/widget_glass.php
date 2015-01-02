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
        $rateInfo = api::rateInfo($currentRate['firstCurrency'], $currentRate['secondCurrency']);

        $maxPrice = $rateInfo['bid'];
        $minPrice = $rateInfo['ask'];
        $totalPrice = $rateInfo['total_price'];
        $totalVolume = $rateInfo['total_volume'];

        $user = usr::getCurrentUser(1);
        if ($user!=null)
        {
            $userFunds = usr::getCurrentUsersPurses();

            $userFirstCurrFundsIndex = Core::array_search($userFunds,'CurName', $currentRate['firstCurrency']);
            $userFirstCurrFunds = ($userFirstCurrFundsIndex == -1) ? 0 : $userFunds[$userFirstCurrFundsIndex]['Value'];

            $userSecondCurrFundsIndex = Core::array_search($userFunds,'CurName', $currentRate['secondCurrency']);
            $userSecondCurrFunds = ($userSecondCurrFundsIndex == -1) ? 0 : $userFunds[$userSecondCurrFundsIndex]['Value'];
        }
        else
        {
            $userFirstCurrFunds = 0;
            $userSecondCurrFunds = 0;
        }

        if (isset($currentRate['limit']))
            $depth = api::depth($currentRate['firstCurrency'], $currentRate['secondCurrency'], $currentRate['limit']);
        else
            $depth = api::depth($currentRate['firstCurrency'], $currentRate['secondCurrency']);
    }
 ?>
<div id="leftWidgetGlass" class="widgetglass-part left col-xs-4">
    <div class="widgetglass-title title greenMark"><b><?php print Core::translateToCurrentLocale('Buy'); ?></b> <?php print $currentRate['firstCurrency']; ?> <?php print Core::translateToCurrentLocale('with'); ?> <?php print $currentRate['secondCurrency']; ?></div>
    <div class="widgetglass-block">
        <div class="widgetglass-topinfo clearfix">
            <div class="widgetglass-money clearfix left" style="margin-right: 0px;">
                <div class="widgetglass-moneytitle"><?php print Core::translateToCurrentLocale('Your funds'); ?>:</div><br>
                <div class="widgetglass-moneyicon"><img src="public/img/curr/<?php print $currentRate['secondCurrency'];?>.png"></div>
                <div class="widgetglass-moneyvalue"><b><span id="cur1"><?php print $userSecondCurrFunds; ?></span></b></div>
                <div class="widgetglass-moneycurr"><b><span id="curname1"><?php print $currentRate['secondCurrency'];?></b></span></div>
            </div>
            <div class="widgetglass-money right clearfix" style="margin-right: 3px;">
                <div class="widgetglass-moneytitle"><?php print Core::translateToCurrentLocale('Min price'); ?>:</div><br>
                <div class="widgetglass-moneyicon"><img src="public/img/curr/<?php print $currentRate['secondCurrency'];?>.png"></div>
                <div class="widgetglass-moneyvalue"><b><span id="min_price"><?php print $minPrice; ?></b></span></div>
                <div class="widgetglass-moneycurr"><b><?php print $currentRate['secondCurrency'];?></b></div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="widgetglass-deal">
            <form id="b_form" class="b_form">
                <input type="hidden" name="type" value="buy">
                <input type="hidden" name="firstCurrency" value="<?php print $currentRate['firstCurrency'];?>">
                <input type="hidden" name="secondCurrency" value="<?php print $currentRate['secondCurrency'];?>">
                <div class="table-responsive">
                    <table>
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td><?php print Core::translateToCurrentLocale('Amount'); ?> <b><?php print $currentRate['firstCurrency'];?></b>:</td>
                                <td style="padding-left: 10px;"><input id="b_amount" class="b_amount" type="text" name="amount" value="0" onclick="(function(obj){if (obj.value==0) obj.value=''; })(this);" onkeyup="allcalc('buy');" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><?php print Core::translateToCurrentLocale('Price for '); ?> <b><?php print $currentRate['firstCurrency'];?></b>: </td>
                                <td style="padding-left: 10px;"><input id="b_price" class="b_price" type="text" name="rate" onkeyup="allcalc('buy');" value="<?php print $minPrice; ?>" autocomplete="off"></td> <td><b><?php print $currentRate['secondCurrency'];?></b></td>
                            </tr>
                            <tr class="bold">
                                <td class="greenMark"><?php print Core::translateToCurrentLocale('Total'); ?>:</td>
                                <td style="padding-left: 10px;"><span id="b_all" class="b_all">0</span> <?php print $currentRate['secondCurrency'];?></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <input type="submit" value="<?php print Core::translateToCurrentLocale('Buy'); ?> <?php print $currentRate['firstCurrency'];?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>

        </div>

        <?php
            $order_priority = new OrderPriority();
            $priorities = $order_priority->getAll();
        ?>

        <div class="widgetglass-list">
            <h4 class="greenMark"><b><?php print Core::translateToCurrentLocale('Sell orders'); ?></b></h4>
            <div class="widgetglass-list-total">
                <?php print Core::translateToCurrentLocale('Total'); ?>: <b><span id="totalvolume"><?php print $totalVolume; ?></span> <?php print $currentRate['firstCurrency'];?></b>
            </div>
            <div class="fixed-table-container">
                <div class="table-responsive">
                    <table id="leftGlassList" class="table table-hover table-condensed" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="firstColumn">
                                    <div class="th-inner">
                                        <span><?php print Core::translateToCurrentLocale('Price'); ?>:</span>
                                        <span class="sortArrow">&nbsp;</span>
                                    </div>
                                </th>
                                <th class="secondColumn">
                                    <div class="th-inner">
                                        <span><?php print $currentRate['firstCurrency'];?>:</span>
                                        <span class="sortArrow">&nbsp;</span>
                                    </div>
                                </th>
                                <th class="thirdColumn">
                                    <div class="th-inner">
                                        <span><?php print $currentRate['secondCurrency'];?>:</span>
                                        <span class="sortArrow">&nbsp;</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- here -->
                            <?php if(isset($depth['asks'])): ?>
                                <?php foreach($depth['asks'] as $value):
                                    $part = ($value[1] / $totalVolume) * 100.0;
                                    unset($color);

                                    foreach($priorities as $prior)
                                    {
                                        if ($part > $prior['from'] && $part <= $prior['to'])
                                            $color = $prior['color'];
                                    }
                                    ?>
                                    <tr class="order" onclick="set_price('ask',<?php print $value[0];?>,<?php print $value[1]+$volume; $volume = $volume + $value[1];?>,<?php print $value[0]*$value[1]+$price; $price = $price+$value[0]*$value[1];?>)" title="Total <?php print $currentRate['firstCurrency'];?>: <?php print $volume;?>, Total <?php $currentRate['secondCurrency'];?>: <?php print $price;?>">
                                        <td class="firstColumn">
                                            <span style="color: <?php print $color; ?>"><?php print $value[0];?></span>
                                        </td>
                                        <td>
                                            <span style="color: <?php print $color; ?>"><?php print $value[1];?></span>
                                        </td>
                                        <td>
                                            <span style="color: <?php print $color; ?>"><?php print $value[0]*$value[1];?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="rightWidgetGlass" class="widgetglass-part right col-xs-4">
    <div class="widgetglass-title title greenMark"><b><?php print Core::translateToCurrentLocale('Sell'); ?></b> <?php print $currentRate['firstCurrency'];?> <?php print Core::translateToCurrentLocale('with'); ?> <?php print $currentRate['secondCurrency']; ?></div>
    <div class="widgetglass-block">
        <div class="widgetglass-topinfo clearfix">
            <div class="widgetglass-money clearfix left" style="margin-right: 0px;">
                <div class="widgetglass-moneytitle"><?php print Core::translateToCurrentLocale('Your funds'); ?>:</div><br>
                <div class="widgetglass-moneyicon"><img src="public/img/curr/<?php print $currentRate['firstCurrency'];?>.png"></div>
                <div class="widgetglass-moneyvalue"><b><span id="cur2" class="money_btc"><?php print $userFirstCurrFunds; ?></b></span></div>
                <div class="widgetglass-moneycurr"><b><span id="curname2"><?php print $currentRate['firstCurrency'];?></span></b></div>
            </div>
            <div class="widgetglass-money clearfix right" style="margin-right: 3px;">
                <div class="widgetglass-moneytitle"><?php print Core::translateToCurrentLocale('Max price'); ?>:</div><br>
                <div class="widgetglass-moneyicon"><img src="public/img/curr/<?php print $currentRate['secondCurrency'];?>.png"></div>
                <div class="widgetglass-moneyvalue"><b><span id="max_price"><?php print $maxPrice; ?></span></b></div>
                <div class="widgetglass-moneycurr"><b><?php print $currentRate['secondCurrency'];?></b></div>
            </div>
        </div>
        <div class="widgetglass-deal">
            <form id="s_form" class="s_form">
                <input type="hidden" name="type" value="sell">
                <input type="hidden" name="firstCurrency" value="<?php print $currentRate['firstCurrency'];?>">
                <input type="hidden" name="secondCurrency" value="<?php print $currentRate['secondCurrency'];?>">
                <div class="table-responsive">
                    <table>
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td><?php print Core::translateToCurrentLocale('Amount'); ?> <b><?php print $currentRate['firstCurrency'];?></b>:</td>
                                <td style="padding-left: 10px"><input id="s_amount" class="s_amount" type="text" name="amount" value="0" onkeyup="allcalc('sell');" onclick="(function(obj){if (obj.value==0) obj.value=''; })(this);" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><?php print Core::translateToCurrentLocale('Price for'); ?> <b><?php print $currentRate['firstCurrency'];?></b>: </td>
                                <td style="padding-left: 10px;"><input id="s_price" class="s_price" type="text" name="rate" value="<?php print $maxPrice; ?>" onkeyup="allcalc('sell');" autocomplete="off"></td> <td><b><?php print $currentRate['secondCurrency'];?></b></td>
                            </tr>
                            <tr class="bold">
                                <td class="greenMark"><?php print Core::translateToCurrentLocale('Total'); ?>:</td>
                                <td style="padding-left: 10px;"><span id="s_all" class="s_all">0</span> <?php print $currentRate['secondCurrency'];?></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <input type="submit" value="<?php print Core::translateToCurrentLocale('Sell'); print ' ' . $currentRate['firstCurrency'];?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="widgetglass-list">
            <h4 class="greenMark"><b><?php print Core::translateToCurrentLocale('Buy orders'); ?></b></h4>
            <div class="widgetglass-list-total">
                <?php print Core::translateToCurrentLocale('Total'); ?>: <b><span id="totalprice"><?php print $totalPrice; ?></span> <?php print $currentRate['secondCurrency'];?></b>
            </div>
            <div class="fixed-table-container">
                <div class="table-responsive">
                    <table id="rightGlassList" class="table table-hover table-condensed" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="firstColumn">
                                    <div class="th-inner">
                                        <span><?php print Core::translateToCurrentLocale('Price'); ?>:</span>
                                        <span class="sortArrow">&nbsp;</span>
                                    </div>
                                </th>
                                <th class="secondColumn">
                                    <div class="th-inner">
                                        <span><?php print $currentRate['firstCurrency'];?>:</span>
                                        <span class="sortArrow">&nbsp;</span>
                                    </div>
                                </th>
                                <th class="thirdColumn">
                                    <div class="th-inner">
                                        <span><?php print $currentRate['secondCurrency'];?>:</span>
                                        <span class="sortArrow">&nbsp;</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($depth['bids'])): ?>
                                <?php unset($volume); ?>
                                <?php unset($price); ?>
                                <?php foreach($depth['bids'] as $value):
                                    //$volume = $value[0]*$value[1];
                                    $part = ($value[0]*$value[1] / $totalPrice) * 100.0;
                                    unset($color);

                                    foreach($priorities as $prior)
                                    {
                                        if ($part > $prior['from'] && $part <= $prior['to'])
                                            $color = $prior['color'];
                                    }
                                    ?>
                                    <tr class="order" onclick="set_price('bid',<?php print $value[0];?>,<?php print $value[1]+$volume; $volume = $volume + $value[1];?>,<?php print $value[0]*$value[1]+$price; $price = $price+$value[0]*$value[1];?>)" title="Total <?php print $value['currency_buy'];?>: <?php print $volume;?>, Total <?php print $currentRate['secondCurrency'];?>: <?php print $price;?>">
                                        <td class="firstColumn">
                                            <span style="color: <?php print $color; ?>"><?php print $value[0];?></span>
                                        </td>
                                        <td>
                                            <span style="color: <?php print $color; ?>"><?php print $value[1];?></span>
                                        </td>
                                        <td>
                                            <span style="color: <?php print $color; ?>"><?php print $value[0]*$value[1];?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
