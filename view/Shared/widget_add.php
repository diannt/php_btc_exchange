<?php
$rates = api::getAllCurrencies();
?>
<!-- onclick="chooseFirst(this);" -->
<div class="modal-dialog">
    <div class="modal-content signin-block">
        <div class="modal-header regTitle">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <?php print Core::translateToCurrentLocale('Add currency pair'); ?>
        </div>
        <div id="widgetadd" class="modal-body">
            <ul class="exchangeInfoContainer">
                <?php foreach($rates as $rate): ?>
                    <li class="<?php print $rate['Name']; ?>">
                        <img style="margin-top: -3px;" src="/public/img/curr/<?php print $rate['Name']; ?>.png">
                        <b><span class="currencyName"><?php print $rate['tradeName']; ?></span></b>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="widgetExchangeFirst clearfix">
                <div class="exchangeSearchZoom"></div>
                <input class="exchangeSearch" value="<?php print $rates[0]['tradeName'] ?>">
                <div class="exchangeButton">&#9660;</div>
                <ul class="exchangeMenu"></ul>
            </div>
            <div class="widgetExchangeSecond clearfix">
                <div class="exchangeSearchZoom"></div>
                <input class="exchangeSearch" value="<?php print $rates[1]['tradeName'] ?>">
                <div class="exchangeButton">&#9660;</div>
                <ul class="exchangeMenu"></ul>
            </div>
            <div class="widgetExchangeButton">
                <button id="submit" type="button" class="btn btn-primary btn-lg" onclick="addPage();"><?php print Core::translateToCurrentLocale('ADD'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $.fn.ready(function(){

        var container = $('.exchangeInfoContainer .currencyName');

        $(".exchangeSearch").keyup(function(){
            var menu = $(this).parent().find('.exchangeMenu');
            menu.empty();
            menu.show();

            var valueStr = $(this).val().toLowerCase();

            for(var i = 0; i != container.length; i++)
            {
                var rawStr = container[i].innerText;
                var str = rawStr.toLowerCase();
                if((str.indexOf(valueStr) + 1) == 1)
                {
                    var html = $('.exchangeInfoContainer .currencyName:contains("' + rawStr + '")').parent().parent().clone();
                    html.bind('mouseover', function(){
                        $(this).addClass('active');
                    });

                    html.bind('mouseleave', function(){
                        $(this).removeClass('active');
                    });

                    html.bind('click', function(){
                        $(this).parent().parent().find('.exchangeSearch').val($(this).find(".currencyName").html());


                        if($(this).parent().parent().hasClass('widgetExchangeFirst') == 1)
                            chooseFirst($(this));
                        else
                            chooseSecond($(this));
                    });

                    $(this).parent().find('.exchangeMenu').append(html);
                }
            }
            menu.bind('mouseleave', function(){$(this).hide();});

        });


        $('.exchangeButton').click(function(){
            var menu = $(this).parent().find('.exchangeMenu');
            menu.empty();
            menu.show();

            for(var i = 0; i != container.length; i++)
            {
                var rawStr = $(container[i]).html();
                var html = $('.exchangeInfoContainer .currencyName:contains("' + rawStr + '")').parent().parent().clone();
                html.bind('mouseover', function(){
                    $(this).addClass('active');
                });

                html.bind('mouseleave', function(){
                    $(this).removeClass('active');
                });

                html.bind('click', function(){
                    //$(".exchangeSearch").val($(this).find(".currencyName").html());
                    $(this).parent().parent().find('.exchangeSearch').val($(this).find(".currencyName").html());
                    if($(this).parent().parent().hasClass('widgetExchangeFirst') == 1)
                        chooseFirst($(this));
                    else
                        chooseSecond($(this));
                });

                $(this).parent().find('.exchangeMenu').append(html);
            }

            menu.bind('mouseleave', function(){$(this).hide();});

        });


    });


    var WidgetsLoadOptions = {
        firstCurrency:"<?php print $rates[0]['Name']; ?>",
        secondCurrency:"<?php print $rates[1]['Name']; ?>"
    };

    function chooseFirst(obj)
    {
        obj.removeClass('active');
        WidgetsLoadOptions.firstCurrency = obj[0].className;
        $(".widgetExchangeFirst .exchangeMenu").hide();
    }

    function chooseSecond(obj)
    {
        obj.removeClass('active');
        WidgetsLoadOptions.secondCurrency = obj[0].className;
        $(".widgetExchangeSecond .exchangeMenu").hide();
    }


    function addPage()
    {
        var widgetsLoad = new WidgetsLoad(WidgetsLoadOptions);
        widgetsLoad.Add();
    }
</script>