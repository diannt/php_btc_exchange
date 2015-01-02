<?php $wallets = $data['wallets']; ?>
<div class="wallets-block">
    <h4><span id="addWalletButton"><?php print Core::translateToCurrentLocale('Add wallet'); ?></span></h4>
    <div id="adminNewWallet">
        <form class="form-horizontal" role="form" name="newWallet" method="post" action="/admin/addNewBTCWallet">
            <div class="form-group">
                <label for="account"><?php print Core::translateToCurrentLocale('Type account'); ?></label>
                <input id="account" type="text" name="ACCOUNT">
            </div>
            <div class="form-group">
                <label for="share"><?php print Core::translateToCurrentLocale('Type share'); ?> (%):</label>
                <input id="share" type="text" name="SHARE">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default input-sm"><?php print Core::translateToCurrentLocale('Add wallet'); ?></button>
            </div>
        </form>
    </div>
    <div class="YMWalletsList">
        <?php if(count($wallets) > 0): ?>
            <?php foreach($wallets as $key => $value): ?>
                <div class="YMWallet">
                    Account: <?php print $value['account']; ?><br>
                    Initial Balance: <span style="font-weight: bold"><?php print $value['value']; ?></span><br>
                    Share: <?php print ($value['share'] * 100.0); ?>%<br>
                    Balance: <span style="font-weight: bold; color: red"><?php print $value['current_balance']; ?></span><br>
                    Profit: <?php print $value['profit']; ?>
                </div>
            <?php endforeach; else: ?>
            <img src="/public/img/other/not.png"> No wallets available
        <?php endif; ?>
    </div>
</div>
<script>
    $.fn.ready(function(){
        $('#addWalletButton').click(function(){
            var block = $("#adminNewWallet");

            if(block.css('display') == 'none')
                $("#adminNewWallet").slideDown();
            else
                $("#adminNewWallet").slideUp();
        });
    });
</script>
