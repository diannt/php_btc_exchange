var TradeHistory = TradeHistory || (function () {
    // Constructor.
    function TradeHistory(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
        this.count = ctor.count || '';
    }
    //
    TradeHistory.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency" : "'+this.firstCurrency+'","secondCurrency": "'+this.secondCurrency+'","count":"'+this.count+'"}}');
        PageLoader.loadPart("widgettradehistory", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                             //there is no ajaxloader
    };
    //
    TradeHistory.prototype.Refresh = function (firstCurrency, secondCurrency) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency" : "'+firstCurrency+'","secondCurrency": "'+secondCurrency+'","count":"'+this.count+'"}}');
        PageLoader.loadPart("widgettradehistory", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                             //there is no ajaxloader
    };
    //

    return TradeHistory;
})();
