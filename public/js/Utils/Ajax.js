///<reference path="PageLoader.js" />

var Ajax = Ajax || (function () {
    var Ajax = {
        exec: function () {
            this.execPost.apply(this, arguments);
        },
        execPost: function (url, params, callbackSuccess, callbackError) {
            if (typeof callbackError === "function") {
                callbackError = defaultError;
            }
            
            return $.ajax({
                type: "POST",
                url: url,
                data: params,
                dataType: "json",
                success: function (response) {
                    ajaxHasExecuted(response, callbackSuccess);
                },
                error: callbackError
            });
        },
        execGet: function (url, params, callbackSuccess, callbackError) {
            if (typeof callbackError === "function") {
                callbackError = defaultError;
            }

            return $.ajax({
                type: "GET",
                url: url,
                data: params,
                dataType: "json",
                success: function (response) {
                    ajaxHasExecuted(response, callbackSuccess);
                },
                error: callbackError
            });
        },
        execGetAsyncParam: function (url, params,isAsync, callbackSuccess, callbackError) {
            if (typeof callbackError === "function") {
                callbackError = defaultError;
            }

            return $.ajax({
                type: "GET",
                async: isAsync,
                url: url,
                data: params,
                dataType: "json",
                success: function (response) {
                    ajaxHasExecuted(response, callbackSuccess);
                },
                error: callbackError
            });
        },
        JSONP: function (url, callbackSuccess, callbackError) {
            if (typeof callbackError === "function") {
                callbackError = defaultError;
            }

            return $.getJSON(url + '&callback=?',
                function (response) {
                    ajaxHasExecuted(response, callbackSuccess);
                }
            );
        },
        getHTML: function (url, params, callbackSuccess, callbackError) {
            if (typeof callbackError === "function") {
                callbackError = defaultError;
            }

            return $.get(url, $.param(params),
                function (response) {
                    ajaxHasExecuted(response, callbackSuccess);
                }
            );
        }
    };


    function ajaxHasExecuted(response, callbackSuccess) {
        response = response || {};

        if (response.RedidrectUrl) {
            location = response.RedidrectUrl;
        }
        else if (response.Error) {

            //MessageText,IsSticky,position,type

            if (typeof callbackSuccess === "function") {
                callbackSuccess(response);
            }
        }
        else if (response.IsAjaxLoad) {
            callbackSuccess(response);
        }
        else if (response.IsAjaxLoadPartial) {
            callbackSuccess(response);
        }
        else {
            if (typeof callbackSuccess === "function") {
                callbackSuccess(response);
            }
        }
    }

    function defaultError(e) {
        alert(e);
        console.log(e);
    }

    return Ajax;
})();

$.ajaxSetup({
    cache: false,
    error: function (xmlHttpRequest, textStatus, errorThrown) {
        //alert(xmlHttpRequest + "\n" + textStatus + "\n" + errorThrown + "\n" + xmlHttpRequest.responseText);
        //WindowOpen(xmlHttpRequest.responseText);
        //console.log(xmlHttpRequest);
        //console.log(textStatus);
        //console.log(errorThrown);
        
    }
});

function WindowOpen(htmlText) {
    myWin = open("", "displayWindow","width=800,height=600,status=yes,toolbar=yes,menubar=yes");
    
    myWin.document.open();
   
    myWin.document.write(htmlText);
    
   myWin.document.close();
}