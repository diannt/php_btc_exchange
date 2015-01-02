<script>
    function definePaymentId(elem)
    {
        var form = $(elem);
        var amount = form.find("input[name=PAYMENT_AMOUNT]").val();
        var units = form.find("input[name=PAYMENT_UNITS]").val();
        var success = false;

        $.ajax({
            async: false,
            type: 'POST',
            url: '/money/PM_transaction',
            data: {
                amount: amount,
                units: units
            },
            dataType: 'json',
            success: function(result) {
                if (result.success == 1){
                    form.find("input[name=PAYMENT_ID]").val(result.PAYMENT_ID);
                    form.find("input[name=PAYEE_ACCOUNT]").val(result.PAYEE_ACCOUNT);
                    success = true;
                } else {
                    window.location = result.error;
                }
            }
        });

        if (!success){
            event.preventDefault();
        }
    }

    function submitPMOutput(elem)
    {
        event.preventDefault();

        var form = $(elem);
        var url = form.attr('action');
        var currency = form.find("input[name=fiat]").val();
        var amount = form.find("input[name=amount]").val();
        var account = form.find("input[name=account]").val();
        console.log(currency);

        $.ajax({
            async: false,
            type: 'POST',
            url: url,
            data: {
                currency: currency,
                account: account,
                amount: amount
            },
            dataType: 'json',
            success: function(result) {
                window.location = result.location;
            }
        });
    }
</script>

<form onsubmit="definePaymentId(this);" method="POST" class="form-money form-horizontal in" role="form" name='PM_in' style="display: none" action="https://perfectmoney.is/api/step1.asp">
    <input type="hidden" name="PAYEE_ACCOUNT" value="">
    <input type="hidden" name="PAYEE_NAME" value="Emonex">
    <input type="hidden" name="PAYMENT_ID" value=""><br>
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Amount'); ?>:</div>
    <input type="text" name="PAYMENT_AMOUNT" value=""><br>
    <input type="hidden" name="PAYMENT_UNITS" value="<?php print $data['CurName']; ?>">
    <input type="hidden" name="STATUS_URL" value="<?php print 'http://' . Core::server_url() . '/money/PM_transaction' . '_status'; ?>">
    <input type="hidden" name="PAYMENT_URL" value="<?php print 'http://' . Core::server_url() . '/money/PM_transaction' . '_success'; ?>">
    <input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
    <input type="hidden" name="NOPAYMENT_URL" value="<?php print 'http://' . Core::server_url() . '/money/PM_transaction' . '_cancel'; ?>">
    <input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
    <input class="btn" type="submit" value="<?php print Core::translateToCurrentLocale('In'); ?>">
</form>

<form onsubmit="submitPMOutput(this);" method="POST" class="form-money form-horizontal" role="form" name='PM_out' style="display: none" action="/money/PM_transaction_o">
    <input type="hidden" name="fiat" value="<?php print $data['CurName']; ?>">
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Amount'); ?>:</div>
    <input type="text" name="amount" value=""><br>
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Account'); ?>:</div>
    <input type="text" name="account" value=""><br>
    <input class="btn" type="submit" value="<?php print Core::translateToCurrentLocale('Out'); ?>">
</form>