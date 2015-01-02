var Balance = Balance || (function () {
    // Constructor.
    function Balance(ctor) {
        ctor = ctor || {};

        this.getUrl = ctor.getUrl || '';
        this.Params = ctor.Params || '';

    }
    //
    Balance.prototype.Init = function () {
        //Start View
        Ajax.execGet(this.getUrl, this.Params, function(result)
        {
            PageLoader.loadPart("balancePanel", "api/account_balance", result, true, function () { //if you choose false in parameters - then
            });                                                                                    //there is no ajaxloader
        });

    };
    //
    Balance.prototype.Refresh = function() {
        //Refresh View
        PageLoader.loadPart("balancePanel", "api/account_balance", false, function(){
        });
    };
    //

    return Balance;
})();