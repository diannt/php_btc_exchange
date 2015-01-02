var ordersParams = {
    getUrl : "api/widget_orders",
    cancelUrl : "api/cancelOrder",
    firstCurrency : "",
    secondCurrency : "",
    cancelOrderId : ""
};
var orders = new Orders(ordersParams);
function ordersready()
{
    $(".widgetorders-list .table").tablesorter({dateFormat:"yyyy-mm-dd hh:mm:ss", headers: { 0: {sorter: "shortDate"}, 1: {sorter: false}, 2: {sorter: false}, 3: {sorter: false}, 4: { sorter: false}, 5: {sorter: false} } });
}