<?php
    $return = api::getInfo();
    $cur = new Currency();

?>

<div id="toOtherPlank" class="toOtherPlank">
    <div class="plankTitle"><a href="http://green.emonex.info"><img src="/public/img/other/plank.png"></a></div>
</div>


<?php if (isset($return['return'])): ?>
    <h5><b><?php print Core::translateToCurrentLocale('Your balance'); ?>:</b></h5>
<?php foreach($return['return']['funds'] as $key=>$value): ?>
    <?php if($key == $data[0]['rate']['firstCurrency'] || $key== $data[0]['rate']['secondCurrency']): ?>
        <div class="wallet">
            <div class="wallet-icon left"><img src="/public/img/curr/<?php print $key; ?>.png" style=""></div>
            <div class="wallet-title left"><span id="<?php print $key; ?>" class="greenMark"><?php $cur->findBy(array('Name' => $key)); print $cur->getTradeName(); ?></span></div>
            <br>
            <div class="wallet-list">
                <div class="wallet-list-item">
                    <b id="val<?php print $key; ?>"><?php print $value; ?></b> (<?php print $key; ?>) <img onclick="addMoney('<?php print $key; ?>');" class="wallet-item-add" src="/public/img/other/add.png">
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

<?php if (isset($data)): ?>
<div id="anchorList" class="anchorList">
    <div id="anchor" class="anchor"></div>
    <ul>
    <a href="#topBar"><li onclick="selectAnchor(this);"><?php print Core::translateToCurrentLocale("Dashboard"); ?></li></a>
        <?php foreach($data as $key => $value): ?>
        <?php if (isset($value['widget_info']['widget_name'])): ?>
            <a href="#<?php print $value['widget_info']['widget_name']; ?>"><li onclick="selectAnchor(this);"><?php print Core::translateToCurrentLocale($value['widget_info']['official_name']); ?></li></a>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>