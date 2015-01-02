var Glass = Glass || (function () {
    // Constructor.
    function Glass(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
        this.limit = ctor.limit || '';
    }
    //
    Glass.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "Data": { "firstCurrency": "' + this.firstCurrency +
                                      '","secondCurrency": "' + this.secondCurrency + '", "limit": "' + this.limit+ '"} }');
        PageLoader.loadPart("widgetglass", this.getUrl, Params, true, function () { //if you choose false in parameters - then
        });                                                                                                                //there is no ajaxloader
    };
    //
    Glass.prototype.Refresh = function(firstCurrency, secondCurrency) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data": { "firstCurrency": "' + firstCurrency +
            '","secondCurrency": "' + secondCurrency + '", "limit": "' + this.limit+ '"} }');
        PageLoader.loadPart("widgetglass", this.getUrl, Params, true, function () { //if you choose false in parameters - then
        });                                                                                   //there is no ajaxloader
    };
    //

    return Glass;
})();
