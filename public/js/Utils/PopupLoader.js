var PopupLoader = PopupLoader || (function () {

    // Popup loader class.
    function PopupLoaderObj(ctor) {
        ctor = ctor || {};

        this.id = new Date().getTime();
        this.container = ctor.container || '';
        this.spinner = null;
    };

    // Show popup loader.
    PopupLoaderObj.prototype.show = function () {
        var html = '';

        html = '<div id="popup_loader_' + this.id + '" class="popup-loader"></div>';

        $(this.container).append(html);
        var opts = {
            lines: 9, // The number of lines to draw
            length: 10, // The length of each line
            width: 8, // The line thickness
            radius: 12, // The radius of the inner circle
            rotate: 0, // The rotation offset
            color: '#000', // #rgb or #rrggbb
            speed: 1.4, // Rounds per second
            trail: 70, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: true, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 9999999999, // The z-index (defaults to 2000000000)
            top: 'auto', // Top position relative to parent in px
            left: 'auto' // Left position relative to parent in px
        };

         var target = document.getElementById("popup_loader_" + this.id);
         this.spinner = new Spinner(opts).spin(target);
    };

    // Hide popup loader.
    PopupLoaderObj.prototype.hide = function () {
        this.spinner.stop();
        $("#popup_loader_" + this.id).remove();
    };



    // Popup loader wrapper.
    var PopupLoader = {
        show: function () {
            var popupLoaderObj = new PopupLoaderObj({
                container: '#modalSpinner'
            });
            popupLoaderObj.show();

            return popupLoaderObj;
        }
    };

    return PopupLoader;
})();

function showLoader()
{
    $('#myModal').modal({
        remote: "/api/popup_loader",
        keyboard: false,
        backdrop: 'static'
    });
}

function hideLoader()
{
    $('#myModal').modal('hide');
}