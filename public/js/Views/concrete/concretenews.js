function newsready()
{
    $("#widgetNewsMoreLabel a").click(function(){
        var block = $(this).parent().parent();

        var text = block.find("#widgetNewsItemFull").text();
        block.append(text);
        $(this).parent().empty();
    });
}