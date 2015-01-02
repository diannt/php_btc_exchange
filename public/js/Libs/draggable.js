$.fn.draggable = function(){
    function disableSelection(){
        return false;
    }
    context = $(this);
    $(this.selector + " .drag").mousedown(function(e){
        var drag = $(this).parent();
        console.log(drag);
        var posParentTop = drag.parent().offset().top;
        var posParentBottom = posParentTop + drag.parent().height();
        var posOld = drag.offset().top;
        var posOldCorrection = e.pageY - posOld;
        drag.css({'z-index':2});
        var mouseMove = function(e){
            var posNew = e.pageY - posOldCorrection;
            drag.css({'background-color':'transparent'});
            if (posNew < posParentTop){
                drag.offset({'top': posParentTop});
                if (drag.prev().length > 0 ) {
                    drag.insertBefore(drag.prev().css({'top':-drag.height()}).animate({'top':0}, 100));
                }
            } else if (posNew + drag.height() > posParentBottom){
                drag.offset({'top': posParentBottom - drag.height()});
                if (drag.next().length > 0 ) {
                    drag.insertAfter(drag.next().css({'top':drag.height()}).animate({'top':0}, 100));
                }
            } else {
                drag.offset({'top': posNew});
                if (posOld - posNew > drag.height() - 1){
                    drag.insertBefore(drag.prev().css({'top':-drag.height()}).animate({'top':0}, 100));
                    drag.css({'top':0});
                    posOld = drag.offset().top;
                    posNew = e.pageY - posOldCorrection;
                    posOldCorrection = e.pageY - posOld;
                } else if (posNew - posOld > drag.height() - 1){
                    drag.insertAfter(drag.next().css({'top':drag.height()}).animate({'top':0}, 100));
                    drag.css({'top':0});
                    posOld = drag.offset().top;
                    posNew = e.pageY - posOldCorrection;
                    posOldCorrection = e.pageY - posOld;
                }
            }
        };
        var mouseUp = function(){
            $("#widgetdragcontainer").off('mousemove', mouseMove).off('mouseup', mouseUp);
            drag.animate({'top':0}, 100, function(){
                drag.css({'z-index':1, 'background-color':'transparent'});
            });
            widgetsDrag.Change();
        };
        $("#widgetdragcontainer").on('mousemove', mouseMove).on('mouseup', mouseUp).on('contextmenu', mouseUp);
        $("#widgetdragcontainer").on('mousemove', disableSelection);
        $(window).on('blur', mouseUp);
    });
}