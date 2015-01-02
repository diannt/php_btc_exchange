var WidgetsLoad = WidgetsLoad || (function () {
    //
    function WidgetsLoad(ctor) {
        ctor = ctor || {};

        this.firstCurrency = ctor.firstCurrency || '';
        this.secondCurrency = ctor.secondCurrency || '';
    }
    //
    WidgetsLoad.prototype.Add = function() {
        var Params = jQuery.parseJSON('{ "firstCurrency": "' + this.firstCurrency +
            '","secondCurrency": "' + this.secondCurrency + '" }');
        var context = this;
        Ajax.exec("widgetControl/addPage", Params, function(result){
            if (result.success == 0)
            {
                if (result.code == 1)
                    alert(result.error);
                if (result.code == 2)
                {
                    $('.modal.in').modal('hide');
                    $('.modal-backdrop').hide();
                    $("."+Params.firstCurrency+Params.secondCurrency).click();
                }
            }
            if (result.success == 1)
            {
                context.Refresh(0);
            }
        });
    };
    WidgetsLoad.prototype.Delete = function(page) {
        var Params = jQuery.parseJSON('{ "page" : "'+ page + '"}');
        var context = this;
        Ajax.exec("widgetControl/removePage", Params, function(result){
            if (result.success = 1)
                context.Refresh(0);
        });
    };

    //
    WidgetsLoad.prototype.Refresh = function(page) {
        //Refresh View
        var Params = jQuery.parseJSON('{ "Data" : "' + page +'" }');

        PageLoader.loadPart("main_middle", "api/main_middle", Params, false, function(){
            hideLoader();
        });
    };
    //

    return WidgetsLoad;
})();