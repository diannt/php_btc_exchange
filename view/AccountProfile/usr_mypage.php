<?php
    if(isset($_GET['err']))
    {
        switch($_GET['err'])
        {
            case(0):
                print '
                <div class="alert alert-success">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Success") . '!</strong> ' . Core::translateToCurrentLocale("Payment is done") . '!' .
                '</div>';
                break;
            case(1):
                print '
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Error") . '!</strong> ' . Core::translateToCurrentLocale("Wrong user action") . '!' .
                '</div>';
                break;
            case(2):
                print '
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Error") . '!</strong> ' . Core::translateToCurrentLocale("Wrong money value") . '!' .
                '</div>';
                break;
            case(3):
                print '
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Error") . '!</strong> ' . Core::translateToCurrentLocale("Server error") . '! ' . Core::translateToCurrentLocale("Please try later") . '.' .
                '</div>';
                break;
            case(4):
                print '
                <div class="alert alert-info">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Success") . '!</strong> ' . Core::translateToCurrentLocale("Your account now associated with you purse. Please try again to pay.") .
                '</div>';
                break;
            case(5):
                print '
                <div class="alert alert-info">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Success") . '!</strong> ' . Core::translateToCurrentLocale("Please, check your email for new message!") .
                    '</div>';
                break;
            case(6):
                print '
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Error") . '!</strong> ' . Core::translateToCurrentLocale($_GET['message']) .
                    '</div>';
                break;
            case(7):
                print '
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert">×</a>
                    <strong>' . Core::translateToCurrentLocale("Error") . '!</strong> ' . Core::translateToCurrentLocale("Wrong data input") . '!' .
                    '</div>';
                break;
        }
    }
?>

<div class="wallets-block">
    <?php if(count($data['purses']) > 0): ?>
        <?php foreach($data['purses'] as $value): ?>
            <div class="wallet-item clearfix">
                <div class="wallet-top-wallet-block clearfix">
                    <img align="left" class="wallet-icon" src="/public/img/curr/<?php print $value['CurName']; ?>.png"><div class="wallet-currency-name currency-name left"><?php print $value['tradeName']; ?></div>
                    <div class="left"><?php print Core::translateToCurrentLocale("Value"); ?>: <span class="currency-value"><?php print $value['Value']; ?> <?php print $value['CurName']; ?></span></div>
                    <div class="wallet-actions" id="<?php print $value['CurName']; ?>">
                        <!-- /money/YM_transaction/ -->
                        <input class="inButton btn" type="button" value="<?php print Core::translateToCurrentLocale('In'); ?>">
                        <input class="outButton btn" type="button" value="<?php print Core::translateToCurrentLocale('Out'); ?>">
                    </div>
                </div>
                <div class="alert submitValueDialog">
                    <div class="formArrow"></div>
                    <a class="submitValueDialog-close" ><img src="/public/img/other/close_wallet.png"></a>
                    <?php
                        $systems = CurrencyPaymentSystem::availablePaymentSystems($value['CurName']);
                        if(count($systems) > 1):
                    ?>
                    <select id="payment-select">
                        <?php foreach($systems as $system): ?>
                            <option value="<?php print $system['name']; ?>"><?php print $system['trade_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php else: ?>
                        <select id="payment-select" style="display: none"><option id="payment-select" value="<?php print $systems[0]['name']; ?>"><?php print $system['trade_name']; ?></option></select>
                        <?php print $systems[0]['trade_name']; ?>
                    <?php endif; ?>

                    <?php
                        $input = $value;
                        foreach($systems as $system):
                            $input['URL'] = $system['URL'];
                            Core::runView('PayingPanel/' . $system['name'] . '_in_out', $input);
                        endforeach;
                    ?>
                    <div id="payment-form"></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>
    $.fn.ready(function(){
        $(".submitValueDialog-close").click(function(){
            var Wallet = $(this).parent().parent().parent();
            Wallet.find('.inButton').css('background-color', '#264775');
            Wallet.find('.outButton').css('background-color', '#264775');

            //wallet-actions
            var alert = $(this).parent();
            alert.slideUp("medium", function(){ alert.hide(); });
        });

        $(".outButton").click(function(){
            $(this).parent().parent().parent().find('.formArrow').css('left', '260px');
            $(this).parent().find('.inButton').css('background-color', '#264775');

            var itemBlock = $(this).parent().parent().parent();
            var block = itemBlock.find(".submitValueDialog");
            var paymentSystem = block.find("#payment-select").val();
            var paymentForm = block.find("#payment-form");
            var outForm = block.find("[name='" + paymentSystem + "_out']");

            if(block.css('display') == 'none')
            {
                $(this).css('background-color', '#999999');
                var form = outForm.clone();
                form.show();
                paymentForm.html(form.wrap('<div>').parent().html());

                block.slideDown("medium", function(){ block.show(); });
            }
            else
            {
                block.slideUp("medium", function(){ block.hide(); });
                $(this).css('background-color', '#264775');
            }
        });

        $(".inButton").click(function(){
            $(this).parent().parent().parent().find('.formArrow').css('left', '70px');
            $(this).parent().find('.outButton').css('background-color', '#264775');
            var itemBlock = $(this).parent().parent().parent();
            var block = itemBlock.find(".submitValueDialog");
            var paymentSystemName = block.find("#payment-select").val();
            var paymentForm = block.find("#payment-form");
            var inForm = block.find("[name='" + paymentSystemName + "_in']");
            if(block.css('display') == 'none')
            {

                $(this).css('background-color', '#999999');

                var form = inForm.clone();
                form.show();
                paymentForm.html(form.wrap('<div>').parent().html());

                block.slideDown("medium", function(){ block.show(); });
            }
            else
            {
                $(this).css('background-color', '#264775');
                block.slideUp("medium", function(){ block.hide(); });
            }
        });

//        $('#btc_submit').click(function(){
//            $.ajax({
//                url: '/money/BTC_transaction',
//                dataType : "html",
//                success: function (result){
//                    $('#btc-code-result').html(result);
//                }
//            });
//        });

        $("select").on("change", function() {
            var parentBlock = $(this).parent();
            var paymentSystemName = $(this).val();
            var paymentFormDiv = parentBlock.find('#payment-form');
            var currentForm = paymentFormDiv.find('.form-money');

            var form;
            if (String(currentForm.attr('name')).indexOf('_in') != -1) {
                form = parentBlock.find('form[name=' + paymentSystemName + '_in]');
            } else {
                form = parentBlock.find('form[name=' + paymentSystemName + '_out]');
            }

            form = form.clone();
            form.show();
            paymentFormDiv.html(form.wrap('<div>').parent().html());
        });


        <?php if (isset($_POST['currency'])): ?>
            var currency = <?php print $_POST['currency']; ?>;
            $(currency).find(".inButton").click();
        <?php endif; ?>
    });
</script>
