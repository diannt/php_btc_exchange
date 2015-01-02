var glassParams = {
    getUrl : "api/widget_glass",
    firstCurrency : "",
    secondCurrency : "",
    limit : 50
};
var glass = new Glass(glassParams);

var b_amount, b_price, b_all,
    s_amount, s_price, s_all;
function set_price(type, price, volume)
{
    if (type == 'ask')
    {
        b_amount.val(volume);
        b_price.val(price);
        b_all.html(volume*price);
    }
    if (type == 'bid')
    {
        s_amount.val(volume);
        s_price.val(price);
        s_all.html(volume*price);
    }
}
function allcalc(type)
{
    if (type == 'buy')
    {
        var a = b_amount.val();
        var b = b_price.val();
        b_all.html(a*b);
    }
    if (type == 'sell')
    {
        var a = s_amount.val();
        var b = s_price.val();
        s_all.html(a*b);
    }
}
function trade(form)
{
        var formid = "#"+form;
        var url = "api/trade";
        var data = $(formid).serialize();
        showLoader();
        Ajax.exec(url, data, function(result)
        {
            if (result.success == 0)
            {
                alert(result.error);
                hideLoader();
            }
            if (result.success == 1)
            {
                reloadAllWidgets();
            }
        });
}
function glassready()
{
        b_amount = $("#b_amount"),
        b_price  = $("#b_price"),
        b_all    = $("#b_all"),
        s_amount = $("#s_amount"),
        s_price  = $("#s_price"),
        s_all    = $("#s_all");

    $("#s_form").submit(function(event){
        trade('s_form');
        event.preventDefault();
    });

    $("#b_form").submit(function(event){
        trade('b_form');
        event.preventDefault();
    });

    $("#leftGlassList").tablesorter({widgets: ['zebra'],  headers: { 1: {sorter: false}, 2: {sorter: false} } });
    $("#rightGlassList").tablesorter({widgets: ['zebra'],  headers: { 1: {sorter: false}, 2: {sorter: false} } });
}