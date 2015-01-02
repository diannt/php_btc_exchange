var PageLoader = PageLoader || (function () {
    var PageLoader = {
        loadPart: function (container, url, params, IsLoaderShow, callback) {
            var ajaxLoader;
            if (IsLoaderShow === undefined) IsLoaderShow = true;
            params = params || {};

            if (IsLoaderShow) ajaxLoader = AjaxLoader.show(container);
            Ajax.getHTML(url, params, function (result) {
                if (IsLoaderShow) ajaxLoader.hide();

                $("#" + container).html(result);

                if (typeof callback === "function") {
                    callback(result);
                }
            });
        }
    };

    return PageLoader;
})();