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
    }
    $id = $currentRate['firstCurrency'] . $currentRate['secondCurrency'];
?>
<div class="title greenMark"><?php print Core::translateToCurrentLocale('Special kinds of deals'); ?>:</div>
<form class="specialtrade">
    <input type="hidden" name="firstCurrency" value="<?php print $currentRate['firstCurrency'];?>">
    <input type="hidden" name="secondCurrency" value="<?php print $currentRate['secondCurrency'];?>">
    <span class="trade-radio">
        <label><input name="type" class="buy" type="radio" value="buy" checked="checked" onclick='(function(obj){$(obj).closest("div").children().find(".Limit").val("BuyLimit"); $(obj).closest("div").children().find(".Stop").val("BuyStop"); $(obj).closest("div").children().find(".price").attr("placeholder","<?php print $maxPrice; ?>"); $(obj).closest("div").children().find(".submit").val("Buy");})(this);'> Buy</label>
        <label><input name="type" class="sell" type="radio" value="sell" onclick='(function(obj){$(obj).closest("div").children().find(".Limit").val("SellLimit"); $(obj).closest("div").children().find(".Stop").val("SellStop"); $(obj).closest("div").children().find(".price").attr("placeholder","<?php print $minPrice; ?>"); $(obj).closest("div").children().find(".submit").val("Sell");})(this);'> Sell</label>
	</span>
    <input type="text" name="amount" value="0">
    <input type="text" class="price" name="price" placeholder="<?php print $maxPrice; ?>">
    <select name="kind" onchange="(function(obj){var a = obj.options[obj.selectedIndex].value; if (a=='Market') {$(obj).closest('div').children().find('.price').attr('disabled','disabled'); $(obj).closest('form').attr('action','api/marketOrder');} else {$(obj).closest('div').children().find('.price').removeAttr('disabled'); $(obj).closest('form').attr('action','api/pendingOrder');} })(this);">
        <option class="Limit" value="BuyLimit">Limit</option>
        <option value="Market" onclick='(function(obj){})(this);'>Market</option>
        <option class="Stop" value="BuyStop">Stop</option>
    </select>
    <label><input type="checkbox" value="TakeProfit" onclick='(function(obj){ $(obj).closest("div").children().find(".take").show(); $(obj).closest("div").children().find(".takeProfit").removeAttr("disabled"); $(obj).closest("div").children().find(".takeProfitPrice").removeAttr("disabled"); })(this);'> TakeProfit</label>
    <div class="take" style='display:none'>
        <input class="takeProfit" type="hidden" name="takeProfit" value="1" disabled>
        <label><input class="takeProfitPrice" type="text" name="takeProfitPrice" value="" disabled> TakeProfit price</label>
    </div>
    <label><input type="checkbox" value="StopLoss" onclick='(function(obj){$(obj).closest("div").children().find(".stop").show();$(obj).closest("div").children().find(".stopLoss").removeAttr("disabled"); $(obj).closest("div").children().find(".stopLossPrice").removeAttr("disabled");  })(this);'> StopLoss</label>
    <div class="stop" style='display:none'>
        <input class="stopLoss" type="hidden" name="stopLoss" value="1" disabled>
        <label><input class="stopLossPrice" type="text" name="stopLossPrice" value="" disabled> StopLoss price</label>
        <label><input type="checkbox" value="Trailing Stop" onclick='(function(obj){$(obj).closest("div").find(".trailing").show(); $(obj).closest("div").find(".trailingStop").removeAttr("disabled"); })(this);'> Trailing Stop</label>
        <div class="trailing" style='display:none'>
            <input class="trailingStop" type="hidden" name="trailingStop" value="1" disabled>
            <label><input type="text" name="X" value="1"> X</label>
            <label><input type="text" name="Y" value="1"> Y</label>
        </div>
    </div>
    <input class="submit" type="submit" value="Buy" onclick="specialtrade();">
</form>