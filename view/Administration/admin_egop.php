<?php
$wallets = $data['wallets'];
?>
<div class="wallets-block">
    <h4><span id="addWalletButton">Add wallet</span></h4>
    <div id="adminNewWallet">
        <form class="form-horizontal" role="form" name="newWallet" method="post" action="/admin/addNewEGOPWallet">
            Email:<br>
            <input type="text" name="EMAIL"><br>
            Currency:<br>
            <select name="CURRENCY">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
            </select><br>
            Share (%):<br>
            <input type="text" name="SHARE"><br><br>
            API ID:<br>
            <input type="password" name="API_ID"><br>
            API Password:<br>
            <input type="password" name="API_PASSWORD"><br>
            Store ID:<br>
            <input type="password" name="STORE_ID"><br>
            Store Password:<br>
            <input type="password" name="STORE_PASSWORD"><br>
            Checksum Key:<br>
            <input type="password" name="CHECKSUM_KEY"><br>
            <input type="submit" value="Add wallet">
        </form>
    </div>
    <div class="YMWalletsList">
        <?php if(count($wallets) > 0): ?>
            <?php foreach($wallets as $key => $value): ?>
                <div class="YMWallet">
                    Email: <?php print $value['email']; ?><br>
                    Currency: <?php print $value['currency']; ?><br>
                    Initial Balance: <span style="font-weight: bold"><?php print $value['value']; ?></span><br>
                    Share: <?php print ($value['share'] * 100.0); ?>%<br>
                    Balance: <span style="font-weight: bold; color: red"><?php print $value['current_balance']; ?></span><br>
                    Profit: <?php print $value['profit']; ?><br><br>
                    API ID: <?php print $value['api_id']; ?><br>
                    API Password: <?php print $value['api_password']; ?><br>
                    Store ID: <?php print $value['store_id']; ?><br>
                    Store Password: <?php print $value['store_password']; ?><br>
                    Checksum Key: <?php print $value['checksum_key']; ?>
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