<?php
$wallets = $data['wallets'];
?>
<div class="wallets-block">
    <h4><span id="addWalletButton">Add wallet</span></h4>
    <div id="adminNewWallet">
        <form class="form-horizontal" role="form" name="newWallet" method="post" action="/admin/addNewOKPWallet">
            Email:<br>
            <input type="text" name="EMAIL"><br>
            Wallet ID:<br>
            <input type="password" name="WALLET_ID"><br>
            API password:<br>
            <input type="password" name="API_PASSWORD"><br>
            Currency:<br>
            <select name="CURRENCY">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
            </select><br>
            Share (%):<br>
            <input type="text" name="SHARE"><br>
            <input type="submit" value="Add wallet">
        </form>
    </div>
    <div class="YMWalletsList">
        <?php if(count($wallets) > 0): ?>
            <?php foreach($wallets as $key => $value): ?>
                <div class="YMWallet">
                    Email: <?php print $value['email']; ?><br>
                    Wallet ID: <?php print $value['wallet_id']; ?><br>
                    API password: <?php print $value['api_password']; ?><br>
                    Currency: <?php print $value['currency']; ?><br>
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