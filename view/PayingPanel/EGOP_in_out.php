<script>
    function defineEgoPayInputs(elem)
    {
        var form = $(elem);
        var amount = form.find("input[name=amount]").val();
        var currency = form.find("input[name=currency]").val();
        var success = false;

        $.ajax({
            async: false,
            type: 'post',
            url: '/money/EGOP_transaction',
            data: {
                amount: amount,
                currency: currency
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                if (result.success == 1){
                    form.find("input[name=store_id]").val(result.store_id);
                    form.find("input[name=cf_1]").val(result.at_id);
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

    function submitEgoPayOutput(elem)
    {
        event.preventDefault();

        var form = $(elem);
        var url = form.attr('action');
        var currency = form.find("input[name=currency]").val();
        var amount = form.find("input[name=amount]").val();
        var email = form.find("input[name=email]").val();
        console.log(currency);

        $.ajax({
            async: false,
            type: 'post',
            url: url,
            data: {
                currency: currency,
                email: email,
                amount: amount
            },
            dataType: 'json',
            success: function(result) {
                window.location = result.location;
            }
        });
    }
</script>

<form onsubmit="defineEgoPayInputs(this);" method="post" class="form-money form-horizontal in" role="form" name='EGOP_in' style="display: none" action="https://www.egopay.com/payments/pay/form">
    <input type="hidden" name="store_id" />
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Amount'); ?>:</div>
    <input type="text" name="amount" value=""/>
    <input type="hidden" name="cf_1" />
    <input type="hidden" name="currency" value="<?php print $data['CurName']; ?>"/>
    <input type="hidden" name="success_url" value="<?php print 'http://' . Core::server_url() . '/money/EGOP_transaction' . '_success'; ?>"/>
    <input type="hidden" name="fail_url" value="<?php print 'http://' . Core::server_url() . '/money/EGOP_transaction' . '_fail'; ?>"/>
    <input type="hidden" name="callback_url" value="<?php print 'http://' . Core::server_url() . '/money/EGOP_transaction' . '_callback'; ?>"/>
    <!--input type="hidden" name="verify" value=""/-->
    <input class="btn" type="submit" value="<?php print Core::translateToCurrentLocale('In'); ?>">
</form>

<form onsubmit="submitEgoPayOutput(this);" method="post" class="form-money form-horizontal" role="form" name='EGOP_out' style="display: none" action="/money/EGOP_transaction_o">
    <input type="hidden" name="currency" value="<?php print $data['CurName']; ?>">
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Amount'); ?>:</div>
    <input type="text" name="amount" value=""><br>
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Account'); ?> (<?php print Core::translateToCurrentLocale('Email'); ?>) : </div>
    <input type="text" name="email" value=""><br>
    <input class="btn" type="submit" value="<?php print Core::translateToCurrentLocale('Out'); ?>">
</form>