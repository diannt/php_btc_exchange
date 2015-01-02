var Graph = Graph || (function () {
    // Constructor.
    function Graph(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
        this.period = ctor.period || '';
        this.interval = ctor.interval || '';
        this.container = ctor.container || '';
        this.params = ctor.params || '';
    }
    //
    Graph.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "Data": { "firstCurrency": "' + this.firstCurrency +
            '","secondCurrency": "' + this.secondCurrency + '", "period": "' + this.period + '", "interval": "' + this.interval + '"} }');
        PageLoader.loadPart("widgetgraph", this.getUrl, Params, true, function () { //if you choose false in parameters - then
        });                                                                         //there is no ajaxloader
    };
    //
    Graph.prototype.Refresh = function(firstCurrency, secondCurrency) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data": { "firstCurrency": "' + firstCurrency +
            '","secondCurrency": "' + secondCurrency + '", "period": "' + this.period + '", "interval": "'+ this.interval + '"} }');
        PageLoader.loadPart("widgetgraph", this.getUrl, Params, true, function () { //if you choose false in parameters - then
        });                                                                                   //there is no ajaxloader
    };
    //

    return Graph;
})();
