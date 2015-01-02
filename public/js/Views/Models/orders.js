var Orders = Orders || (function () {
    // Constructor.
    function Orders(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
        this.cancelUrl = ctor.cancelUrl || '';
    }
    //
    Orders.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency": "'+this.firstCurrency+'","secondCurrency": "'+this.secondCurrency+'"} }');
        PageLoader.loadPart("widgetorders", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                    //there is no ajaxloader
    };
    //
    Orders.prototype.Cancel = function(cancelOrderId) {
        //cancelling order
        var Params = jQuery.parseJSON('{ "order_id" : "'+cancelOrderId+'" }');
        showLoader();
        Ajax.execGet("api/cancelOrder", Params, function(result)
        {
            reloadAllWidgets();
        });
    };
    //
    Orders.prototype.CancelFromHistory = function(cancelOrderId) {
        //cancelling order
        var Params = jQuery.parseJSON('{ "order_id" : "'+cancelOrderId+'" }');
        Ajax.execGet("/api/cancelOrder", Params, function(result)
        {
            location.reload();
        });
    };
    //
    Orders.prototype.Refresh = function (firstCurrency, secondCurrency) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency": "'+firstCurrency+'","secondCurrency": "'+secondCurrency+'"} }');
        PageLoader.loadPart("widgetorders", this.getUrl, Params, false, function () { //if you choose false in parameters - then
        });                                                                                     //there is no ajaxloader
    };
    //

    return Orders;
})();
