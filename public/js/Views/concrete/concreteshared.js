
var eTop, didScroll,
anchorList,
balancePanel, otherPlank;

function setGoodHeight()
{
    var firstColumnHeight = $('html').css('height');
    var topColumn = parseInt(firstColumnHeight);
    firstColumnHeight = parseInt(firstColumnHeight) + 68;
    balancePanel.css('height', firstColumnHeight);

    otherPlank.css('height', firstColumnHeight + 134);
}

function setAnchorsList()
{
    if ($(window).scrollTop() >= eTop)
    {
        var fixedList = $(".fixedList");
        if (fixedList.length==0)
        {
            var widthPanel = balancePanel.width();
            anchorList = $("#anchorList");
            anchorList.css("width",widthPanel);

            var eBottom = $(window).height() - anchorList.height();
            anchorList.addClass("fixedList");
            $(".fixedList").css("bottom",eBottom);
        }
    }
    else
    {
        $(".fixedList").removeClass("fixedList");
        anchorList.css("width","");
        anchorList.css("bottom","");
    }
}

function reloadAllWidgets()
{
    var activePage = $("#pageloader .choosedRate").data("num");
    var widgetsLoad = new WidgetsLoad();
    widgetsLoad.Refresh(activePage);
}

function anchorUpdate(){
    anchorList = $("#anchorList");
    var anchorOffset = anchorList.find('ul .active').offset();
    var commonAnchorOffset = anchorList.offset();
    $('#anchor').css('top', anchorOffset.top - commonAnchorOffset.top + 'px');
}
function selectAnchor(obj)
{
    anchorList.find("ul .active").unbind('mouseover').unbind('mouseleave').removeClass("active");
    $(obj).addClass("active")
    anchorList.find("ul .active").bind('mouseover', function(){ $('#anchor').addClass('anchor_hover'); });
    anchorList.find("ul li").bind('mouseleave' ,function(){ $('#anchor').removeClass('anchor_hover'); });
    $('#anchor').addClass('anchor_hover');
    anchorUpdate();
}

//    function addMoney(obj)
//    {
//        $('#' + obj + 'modalBalance').modal();
//        var form = $('.' + obj + '_key');
//        if(form.css('display') == 'none')
//        {
//            form.find('.in').show();
//            form.slideDown();
//        }
//        else
//        {
//            form.slideUp();
//        }
//    }

//    $(".modal-body select").on("change", function() {
//        var parentBlock = $(this).parent();
//        var paymentSystemName = $(this).val();
//        var paymentFormDiv = parentBlock.find('#payment-form');
//        var currentForm = paymentFormDiv.find('.form-money');
//
//        var form;
//        if (String(currentForm.attr('name')).indexOf('_in') != -1) {
//            form = parentBlock.find('form[name=' + paymentSystemName + '_in]');
//        } else {
//            form = parentBlock.find('form[name=' + paymentSystemName + '_out]');
//        }
//
//        form = form.clone();
//        form.show();
//        paymentFormDiv.html(form.wrap('<div>').parent().html());
//    });


function addMoney(obj)
{
    var form = $('<form action="/usr/mypage" method="post">' +
        '<input type="hidden" name="currency" value="' + obj + '" />' +
        '</form>');
    $('body').append(form);
    $(form).submit();
}

function sharedready()
{
    $($("#anchorList li")[0]).addClass("active");
    anchorList = $("#anchorList");
    balancePanel = $('#balancePanel');
    otherPlank = $('#toOtherPlank');
    anchorUpdate();

    anchorList.find("ul .active").bind('mouseover', function(){ $('#anchor').addClass('anchor_hover'); });
    anchorList.find("ul li").bind('mouseleave' ,function(){ $('#anchor').removeClass('anchor_hover'); });

    eTop = anchorList.offset().top;
    $(window).resize(function(){
        anchorList = $("#anchorList");
        if ( $(window).scrollTop() > $(window).height())
            eTop = $(window).scrollTop() + anchorList.offset().top;
        else
            eTop = anchorList.offset().top;

        setGoodHeight();
        setAnchorsList();
    });

    $(window).scroll(function(){
        setAnchorsList();
    });

    /*var timer = setInterval(function() {
     reloadAllWidgets();

     }, 120000); */

    anchorList.find('a[href*=#]').bind("click", function(e){
        var anchor = $(this);
        var name = anchor.attr('href').replace(new RegExp("#",'g'), '');
        $('html, body').stop().animate({
            scrollTop: $('a[name='+name+']').offset().top
        }, 1000);
        e.preventDefault();
    });
    setGoodHeight();
}