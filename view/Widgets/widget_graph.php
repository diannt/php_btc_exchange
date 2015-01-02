<?php
    if (isset($data['firstCurrency']))
        $currentRate = $data;
    else
        if (isset($_GET['Data']))
        {
            $currentRate = $_GET['Data'];
        }
/*
                [
                "high":
                "low":
                "avg":
                "first":
                "last":
                "vol":
                "vol_cur":
                "time": (unix-time)
                "ask":
                "bid":
                ]
*/
?>

<div class="title greenMark"><?php print Core::translateToCurrentLocale('Price graph'); ?></div>
<div class="right"><img id="barsMe" src="/public/img/other/circleness.png"/></div>
<div id="graphicRender" class="graphicRender"></div>


<script>
        graphParams.params = jQuery.parseJSON('{ "firstCurrency": "' + "<?php print $currentRate['firstCurrency']; ?>" +
        '","secondCurrency": "' + "<?php print $currentRate['secondCurrency']; ?>" + '", "period": "' + graphParams.period + '", "interval": "'+ graphParams.interval + '"}');
</script>