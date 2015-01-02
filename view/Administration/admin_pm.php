<?php
    $wallets = $data['wallets'];
?>
<div class="wallets-block">
    <h4><span id="addWalletButton">Add wallet</span></h4>
    <div id="adminNewWallet">
        <form class="form-horizontal" role="form" name="newWallet" method="post" action="/admin/addNewPMWallet">
            Account ID:<br>
            <input type="text" name="ACCOUNT_ID"><br>
            Pass Phrase:<br>
            <input type="password" name="PASS_PHRASE"><br>
            Alternate Pass Phrase:<br>
            <input type="password" name="ALTERNATE_PASS_PHRASE"><br>
            Units:<br>
            <select name="UNITS">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
            </select><br>
            Account:<br>
            <input type="text" name="ACCOUNT"><br>
            Share (%):<br>
            <input type="text" name="SHARE"><br>
            <input type="submit" value="Add wallet">
        </form>
    </div>
    <div class="YMWalletsList">
        <?php if(count($wallets) > 0): ?>
            <?php foreach($wallets as $key => $value): ?>
                <div class="YMWallet">
                    Account ID: <?php print $value['account_id']; ?><br>
                    Pass Phrase: <?php print $value['pass_phrase']; ?><br>
                    Alternate Pass Phrase: <?php print $value['alternate_pass_phrase']; ?><br>
                    Units: <?php print $value['units']; ?><br>
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