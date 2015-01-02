<script>
    function generateBTCCode(elem)
    {
        var form = $(elem).parent();
        var action = form.attr('action');
        var codeDiv = form.find('#btc-code');
        var pre = codeDiv.find('pre');
        var currentAddress = pre.html();

        $.ajax({
            method: 'post',
            url: action,
            data: {
                currentAddress: currentAddress
            },
            dataType : "json",
            success: function (result){
                console.log(result);
                if (result.success == 1)
                    pre.html(result.message);
                else
                    window.location = result.error;
            }
        });
    }

    function submitBTCOutput(elem)
    {
        event.preventDefault();

        var form = $(elem);
        var url = form.attr('action');
        var address = form.find("input[name=address]").val();
        var amount = form.find("input[name=amount]").val();

        $.ajax({
            async: false,
            type: 'POST',
            url: url,
            data: {
                address: address,
                amount: amount
            },
            dataType: 'json',
            success: function(result) {
                window.location = result.location;
            }
        });
    }
</script>
<form method="get" class="form-money in" name="BTC_in" style="display: none" action="/money/BTC_transaction">
    <div id="btc-code"><pre> </pre></div>
    <input class="btn" onclick="generateBTCCode(this);" type="button" value="<?php print Core::translateToCurrentLocale('Get code'); ?>">
</form>

<form role="form" onsubmit="submitBTCOutput(this);" method="post" class="form-money form-horizontal" name="BTC_out" style="display: none" action="/money/BTC_transaction_o">
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale("Address"); ?>:</div>
    <input type="text" name="address"><br>
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale('Amount'); ?>:</div>
    <input type="text" name="amount"><br>
    <input class="btn" type="submit" value="Submit">
</form>