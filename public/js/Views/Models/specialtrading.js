var SpecialTrading = SpecialTrading || (function () {
    // Constructor.
    function SpecialTrading(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
    }
    //
    SpecialTrading.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency": "'+this.firstCurrency+'","secondCurrency": "'+this.secondCurrency+'"} }');
        PageLoader.loadPart("widgetspecialtrading", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                                               //there is no ajaxloader
    };
    //
    SpecialTrading.prototype.Refresh = function (firstCurrency, secondCurrency) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency": "'+firstCurrency+'","secondCurrency": "'+secondCurrency+'"} }');
        PageLoader.loadPart("widgetspecialtrading", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                                               //there is no ajaxloader
    };
    //

    return SpecialTrading;
})();

