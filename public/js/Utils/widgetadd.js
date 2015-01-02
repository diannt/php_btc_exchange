var WidgetAdd = WidgetAdd || (function () {
    // Constructor.
    function WidgetAdd(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.category = ctor.category || '';

    }
    //
    WidgetAdd.prototype.Init = function () {
        //Start View
        var Params = jQuery.parseJSON('{ "category" : "'+this.category+'" }');
        PageLoader.loadPart("widgetinlist", "api/widget_add", Params, true, function () { //if you choose false in parameters - then
        });                                                                               //there is no ajaxloader
    };
    //

    return WidgetAdd;
})();