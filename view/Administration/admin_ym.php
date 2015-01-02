<?php
    $wallets = $data['wallets'];
?>
<div class="wallets-block">
    <h4><span id="addWalletButton">Add wallet</span></h4>
    <div id="adminNewWallet">
        <form name="newWallet" method="post" action="/admin/addNewYMWallet">
            Number:<br>
            <input type="text" name="number"><br>
            Client ID:<br>
            <input type="text" name="client_id"><br>
            Secret ID:<br>
            <input type="text" name="secret_id"><br>
            <input type="submit" value="Add wallet">
        </form>
    </div>
    <div class="YMWalletsList">
        <?php if(count($wallets) > 0 ): ?>
        <?php foreach($wallets as $key => $value): ?>
            <div class="YMWallet">
                <h4><?php print $value['number']; ?></h4>
                Client ID: <?php print $value['client_id']; ?><br>
                Secret ID: <?php print $value['secret_id']; ?><br>
                Balance: <span style="font-weight: bold"><?php print $value['value']; ?></span>
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