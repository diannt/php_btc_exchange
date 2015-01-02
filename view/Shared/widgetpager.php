<?php

    /*
     * Now it is the widget rate !!!
     *
     * */

    if (isset($data['firstCurrency']))
        $currentRate = $data;
    else
        if (isset($_GET['Data']))
        {
            $currentRate = $_GET['Data'];
        }

    $defaultValue = api::rate($currentRate['firstCurrency'], $currentRate['secondCurrency']);

    $pages = widgetControl::getPages();

    if (count($pages) > 0)
    {
        foreach($pages as $key => $value)
        {
            $firstCur = $value['rate']['firstCurrency'];
            $secondCur = $value['rate']['secondCurrency'];

            $pages[$key]['value'] = api::rate($firstCur, $secondCur);
        }

    }
?>

<div id="pageloader" class="widgetrate clearfix">
    <?php if(isset($pages)): ?>
    <?php foreach($pages as $key => $value): ?>
    <div onclick="selectPage(this);" class="widgetrate-item <?php print $value['rate']['firstCurrency'] . $value['rate']['secondCurrency']; ?>" data-num="<?php print $value['page']; ?>">
        <div class="widgetrate-rate">
            <b><?php print $value['rate']['firstCurrency']; ?></b> / <b><?php print $value['rate']['secondCurrency']; ?></b>
        </div>
        <div class="widgetrate-value">
                <span class="widgetrate-value-numb"><?php print $value['value']; ?></span>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
        <div class="widgetrate-item choosedRate <?php print $value['rate']['firstCurrency'] . $value['rate']['secondCurrency']; ?>" data-num="0">
            <div class="widgetrate-rate">
                <b><?php print $currentRate['firstCurrency']; ?></b> / <b><?php print $currentRate['secondCurrency']; ?></b>
            </div>
            <div class="widgetrate-value">
                <span class="widgetrate-value-numb"><?php print $defaultValue; ?></span>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    <?php if (isset($pages)): ?>
    function selectPage(obj){
        showLoader();
        $("#pageloader").find(".choosedRate").removeClass("choosedRate");
        $(obj).addClass("choosedRate");
        document.title="Bitmonex | "+$(".choosedRate .widgetrate-rate").children()[0].innerHTML+$(".choosedRate .widgetrate-rate").children()[1].innerHTML+" "+$(".choosedRate .widgetrate-value-numb").html();
        reloadAllWidgets();
    }
    function deletePage(obj)
    {
        var widgetsLoad = new WidgetsLoad();
        var page = $(obj).parent().parent().data("num");
        widgetsLoad.Delete(page);
    }

    <?php endif; ?>
    <?php if(isset($currentRate['firstCurrency'])): ?>
    $("#pageloader .<?php print $currentRate['firstCurrency'] . $currentRate['secondCurrency']; ?>").addClass("choosedRate");
    $("#pageloader .choosedRate").attr("onclick","");
    <?php endif; ?>
</script>
