var AjaxLoader = AjaxLoader || (function () {
    function AjaxLoaderObj(ctor) {
        ctor = ctor || {};

        this.id = new Date().getTime();
        this.container = ctor.container || '';
        this.content = ctor.content || '';
    };
    AjaxLoaderObj.prototype.show = function () {
        var html = '',
            width = $("#" + this.container).width(),
            height = $("#" + this.container).height();

        html = '<div id="loader_' + this.id + '" class="ajax-loader" style="width: ' + width + 'px; height: ' + height + 'px;"><img src="/public/img/other/ajax-loader.gif" /></div>';

        $("#" + this.container).addClass("prelative")
            .append(html);

    };
    AjaxLoaderObj.prototype.hide = function () {
        $("#" + this.container).removeClass("prelative");
        $("#loader_" + this.id).remove();
    };

    var AjaxLoader = {
        show: function (container, content) {
            var ajaxLoaderObj = new AjaxLoaderObj({
                container: container,
                content: content
            });
            ajaxLoaderObj.show();

            return ajaxLoaderObj;
        }
    };

    return AjaxLoader;
})();