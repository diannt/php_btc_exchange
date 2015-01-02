<?php
if (isset($data['firstCurrency']))
    $currentRate = $data;
else
    if (isset($_GET['Data']))
    {
        $currentRate = $_GET['Data'];
    }

if (isset($currentRate['firstCurrency']))
    $value = api::rate($currentRate['firstCurrency'], $currentRate['secondCurrency']);

?>
<div class="widgetrate-item">
        <div class="widgetrate-rate">
            <b><?php print $currentRate['firstCurrency']; ?></b> / <b><?php print $currentRate['secondCurrency']; ?></b>
        </div>
        <div class="widgetrate-value">
                <span class="widgetrate-value-numb"><?php print $value; ?></span>
                <div class="widgetrate-close"></div>
        </div>
</div>