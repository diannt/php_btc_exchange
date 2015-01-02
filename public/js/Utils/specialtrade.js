var SpecialTrade = SpecialTrade || (function () {
    // Constructor.
    function SpecialTrade(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
        this.type = ctor.type || '';
        this.rate = ctor.rate || '';
        this.amount = ctor.amount || '';

    }
    //
    SpecialTrade.prototype.Market = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "firstCurrency": "'+this.firstCurrency+'","secondCurrency": "'
            +this.secondCurrency+'", "type": "'+this.type+ '","rate": "'+this.rate+'","amount": "'+this.amount+'"}');
        Ajax.execGet(this.getUrl, Params, function(result)
        {
            if (result.success == 0)
                alert(result.error);
            if (result.success == 1)
            {
                reloadAllWidgets();
            }
        });
    };
    SpecialTrade.prototype.Pending = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "firstCurrency": "'+this.firstCurrency+'","secondCurrency": "'
            +this.secondCurrency+'", "type": "'+this.type+ '","rate": "'+this.rate+'","amount": "'+this.amount+'"}');
        Ajax.execGet(this.getUrl, Params, function(result)
        {
            if (result.success == 0)
                alert(result.error);
            if (result.success == 1)
            {
                reloadAllWidgets();
            }
        });
    };
    //

    return SpecialTrade;
})();

