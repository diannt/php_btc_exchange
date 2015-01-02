var tradehistoryParams = {
    getUrl : "api/widget_tradehistory",
    firstCurrency : "",
    secondCurrency : "",
    count : 50
};
var tradehistory = new TradeHistory(tradehistoryParams);

function tradehistoryready()
{
    $(".widgettradehistory-table .table").tablesorter({dateFormat:"yyyy-mm-dd hh:mm:ss", headers: { 0: {sorter: "shortDate"}, 1: { sorter: false}, 2: {sorter: false}, 3: {sorter: false}, 4: {sorter: false}, 5: {sorter: false} } });
}