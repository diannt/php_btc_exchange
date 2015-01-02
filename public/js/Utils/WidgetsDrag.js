var WidgetsDrag = WidgetsDrag || (function () {
    // Constructor.
    function WidgetsDrag(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
    }
    //
    WidgetsDrag.prototype.Change = function () {
        //Start View
        var widgetsDrag = $(".widgetdrag");
        var arr = [];

        $.each(widgetsDrag, function(i, val){
           if (i!=($(val).data("priory") - 1))
           {
               var obj = {"widgetId":$(val).data("id"),"priority":(i+1)};
               arr.push(obj);
               $(val).data("priory", i+1);
           }
        });
        var Params =jQuery.parseJSON('{"Data":'+JSON.stringify(arr)+'}');
        console.log(Params);

        Ajax.execGet("widgetControl/setWidgetPriory", Params, function(result)
        {
            console.log(result);
        });
    };
    //

    return WidgetsDrag;
})();