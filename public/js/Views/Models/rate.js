var Rate = Rate || (function () {
    // Constructor.
    function Rate(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
    }
    //
    Rate.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency": "'+this.firstCurrency+'","secondCurrency": "'+this.secondCurrency+'"} }');
        PageLoader.loadPart("widgetrate", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                                               //there is no ajaxloader
    };
    //
    Rate.prototype.Refresh = function (firstCurrency, secondCurrency) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data":{ "firstCurrency": "'+firstCurrency+'","secondCurrency": "'+secondCurrency+'"} }');
        PageLoader.loadPart("widgetrate", this.getUrl, Params, false, function () {//if you choose false in parameters - then
        });                                                                                                               //there is no ajaxloader
    };
    //

    return Rate;
})();

