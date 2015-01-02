var rateParams = {
    getUrl : "api/widget_rate",
    firstCurrency : "",
    secondCurrency : ""
};
var rate = new Rate(rateParams);
$.fn.ready(function(){
    $(".widgetrate").click(function(e){
       $(".choosedWidget").removeClass("choosedWidget");
       $(this).children().addClass("choosedWidget");
       document.title="Emonex | "+$(".choosedWidget .widgetrate-rate").children()[0].innerHTML+$(".choosedWidget .widgetrate-rate").children()[1].innerHTML+" "+$(".choosedWidget .widgetrate-value-numb").html();
    });
    $(".widgetrate")[0].click();
});