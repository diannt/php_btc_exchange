var specialtradingParams = {
    getUrl : "api/widget_specialtrading",
    firstCurrency : "",
    secondCurrency : ""
};
var specialtrading = new SpecialTrading(specialtradingParams);
function specialtrade()
{
    var formid = "#widgetspecialtrading .specialtrade";
    $(formid).submit(function() {

        var url = $(formid).attr("action");
        var data = $(formid).serialize();

        Ajax.exec(url, data, function(result)
        {
            if (result.success == 0)
                alert(result.error);
            if (result.success == 1)
            {
                reloadAllWidgets();
            }
        });

        return false;
    });


}