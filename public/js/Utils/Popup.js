///<reference path="Ajax.js" />

/*
 * Popup class.
 */
var PopupObj = PopupObj || (function () {
    // Constructor.
    function PopupObj(ctor) {
        ctor = ctor || {};

        this.popupContainer = $(".popup-wrapper");

        this.id = new Date().getTime();
        this.dataUrl = ctor.dataUrl || '';
        this.width = ctor.width || 0;
        this.height = ctor.height || 0;
        this.obj = ctor.obj || {};
        this.x = ctor.x || 0;
        this.y = ctor.y || 0;

        this.uiPopup = null;
    }

    // Show popup.
    PopupObj.prototype.show = function (callback, closecallback) {
        var context = this,
            html = '';

        Ajax.getHTML(this.dataUrl, context.obj, function (response) {
            if (!response.Error) {
                html = '<div id="popup_' + context.id + '" class="popup">' +
                            response +
                       '</div>';

                context.popupContainer.append(html);

                if (context.x === 0 && context.y === 0) {
                    $('#popup_' + context.id).dialog({
                        width: context.width,
                        height: context.height,
                        modal: true,
                        resizable: false,
                        open: function() {
                            $(".ui-widget-overlay").click(function(){
                                context.hide();
                            });
                        },
                        close: function (event, ui) {
                            context.hide();
                            if (typeof closecallback === "function") {
                                closecallback();
                            }
                        }
                    }).attr("data-corners", false);
                } else {
                    $('#popup_' + context.id).dialog({
                        width: context.width,
                        height: context.height,
                        modal: false,
                        resizable: false,
                        position: [context.x, context.y],
                        overlay: {
                            opacity: 0
                        },
                        open: function() {
                            $(".ui-widget-overlay").click(function(){
                                context.hide();
                            });
                        },
                        close: function (event, ui) {
                            context.hide();
                            if (typeof closecallback === "function") {
                                closecallback();
                            }
                        }
                    }).attr("data-corners", false);
                }



                if (typeof callback === "function") {
                    callback();
                }
            }
            else {
                context.popupContainer.hide();
                context.hide();
            }
        });
    };

    PopupObj.prototype.close = function () {
        $('#popup_' + this.id).dialog("close");
    };

    // Hide popup.
    PopupObj.prototype.hide = function (callback) {
        $('#popup_' + this.id).remove();
        if ($('#popup_' + this.id).length) {
            $('#popup_' + this.id).remove();
        }

        if (typeof this.hideCallback === "function") {
            this.hideCallback();
        }

        if (typeof callback === "function") {
            callback();
        }
    };
    // Callback for hide popup.
    PopupObj.prototype.hideCallback = function (data) {
    };

    return PopupObj;
})();



/*
 * Wrapper for popup class.
 */
var Popup = Popup || (function () {
    var Popup = {
        // Create new popup, show and return him.
        show: function (dataUrl, width, height, callback, x, y, closecallback, obj) {
            var popup = new PopupObj({
                dataUrl: dataUrl,
                width: width,
                height: height,
                obj: obj,
                x: x,
                y: y

            });
            popup.show(callback, closecallback);

            return popup;
        }
    };

    return Popup;
})();