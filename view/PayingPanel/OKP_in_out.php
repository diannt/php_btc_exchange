<script>
    function defineInvoice(elem)
    {
        var form = $(elem);
        var currency = form.find("input[name=ok_currency]").val();
        var success = false;

        $.ajax({
            async: false,
            type: 'POST',
            url: '/money/OKP_transaction',
            data: {
                currency: currency
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                if (result.success == 1){
                    form.find("input[name=ok_receiver]").val(result.receiver);
                    form.find("input[name=ok_invoice]").val(result.invoice);
                    form.find("input[name=ok_item_1_name]").val(result.item_name);
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

    function submitOKPOutput(elem)
    {
        event.preventDefault();

        var form = $(elem);
        var url = form.attr('action');
        var currency = form.find("input[name=currency]").val();
        var email = form.find("input[name=email]").val();
        var amount = form.find("input[name=amount]").val();

        $.ajax({
            async: false,
            type: 'POST',
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

<form onsubmit="defineInvoice(this);" method="post" class="form-money form-horizontal in" role="form" name='OKP_in' style="display: none" action="https://www.okpay.com/process.html">
    <input type="hidden" name="ok_receiver" value=""/>
    <input type="hidden" name="ok_item_1_name" value=""/>
    <input type="hidden" name="ok_currency" value="<?php print $data['CurName']; ?>"/>
    <input type="hidden" name="ok_item_1_type" value="service"/>
    <input type="hidden" name="ok_invoice" value=""/>
    <input type="hidden" name="ok_fees" value="1"/>
    <input type="hidden" name="ok_return_success" value="<?php print 'http://' . Core::server_url() . '/money/OKP_transaction' . '_success'; ?>"/>
    <input type="hidden" name="ok_return_fail" value="<?php print 'http://' . Core::server_url() . '/money/OKP_transaction' . '_fail'; ?>"/>
    <input type="hidden" name="ok_ipn" value="<?php print 'http://' . Core::server_url() . '/money/OKP_transaction' . '_notification'; ?>"/>
    <input class="btn" type="image" name="submit" alt="OKPAY Payment" src="https://www.okpay.com/img/buttons/en/top-up/t12b145x42en.png"/>
</form>

<form onsubmit="submitOKPOutput(this);" method="POST" class="form-money form-horizontal" role="form" name='OKP_out' style="display: none" action="/money/OKP_transaction_o">
    <input type="hidden" name="currency" value="<?php print $data['CurName']; ?>">
    <?php print Core::translateToCurrentLocale('Specify the email address'); ?><br>
    <?php print Core::translateToCurrentLocale('fee okpay - paid by the beneficiary'); ?><br>
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Wallet'); ?>:</div>
    <input type="text" name="email" value=""><br>
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Amount'); ?>:</div>
    <input type="text" name="amount" value=""><br>
    <input class="btn" type="submit" value="<?php print Core::translateToCurrentLocale('Out'); ?>">
</form>