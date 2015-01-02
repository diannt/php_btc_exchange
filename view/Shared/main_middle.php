<?php
    $user = usr::getCurrentUser(1);
    if (isset($data[0]['rate']))
        $widgets = $data;
    else
    if (isset($_GET['Data']))
    {
        $page = $_GET['Data'];
        if ($page!=null)
            $widgets = widgetControl::getPageWidgets($page);
        else
            $widgets = widgetControl::getPageWidgets(0);
    }
?>

<!-- DashBoard -->
<div id="dashboard" class="dashboard col-xs-9">
    <div class="topHead" style="margin-bottom:0px;">
        <span class="greenMark"><?php print Core::translateToCurrentLocale("Dashboard"); ?></span>
        <div class="line"></div>
    </div>
    <div class="widgetPager">
        <?php $data = $widgets[0]['rate']; print Core::runView('Shared/widgetpager', $data); $currentRate = $widgets[0]['rate']; ?>
    </div>
    <?php foreach($widgets as $key => $value): ?>
        <a name="<?php print $value['widget_info']['widget_name']; ?>"></a>
        <div id="widget<?php print $value['widget_info']['widget_name']; ?>" class="widget<?php print $value['widget_info']['widget_name']?> clearfix">
            <?php $data = $value['rate']; print Core::runView('Widgets/widget_' . $value['widget_info']['widget_name'], $data); ?>
        </div>
    <?php endforeach; ?>
</div>


<!-- Balance Panel -->
<div id="balancePanel" class="col-xs-2">
    <?php $data = $widgets; print Core::runView('Shared/account_balance', $data); ?>
</div>


<script type="text/javascript">
$.fn.ready(function(){
    <?php foreach($widgets as $key => $value): ?>
        <?php print $value['widget_info']['widget_name'] . "ready();" ?>
    <?php endforeach; ?>
    sharedready();
    <?php if ($user == null): ?>
        $("#register").click(function(){
            $('#myModal').modal({
                remote: "/api/popup_register"
            });
        });
    <?php endif; ?>
});
<?php if ($user != null): ?>
    var BalanceParams = {
        getUrl : "api/getInfo",
        Params : ""
    };
    var balance = new Balance(BalanceParams);
<?php endif; ?>
</script>