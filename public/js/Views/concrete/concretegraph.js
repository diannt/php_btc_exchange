var graphParams = {
    getUrl : "api/widget_graph",
    firstCurrency : "",
    secondCurrency : "",
    period : 10080,
    interval : 720,
    container : "",
    params: ""
};
//var graph = new Graph(graphParams);
function graphDraw()
{
    var Params = graphParams.params;
    Ajax.exec("api/graph", Params, function(result)
    {
        var d1        = result.ticks1,
            container = document.getElementById("graphicRender"),
            start     = result.ticks1[0][0],
            stop      = result.ticks1[(result.ticks1.length-1)][0],
            //ymin      = result.ticks1[0][1],
            //ymax      = result.ticks1[(result.ticks1.length-1)][1],
            options, graph, x, o,
            context   = this;
        //there should be d2 for results of volumes TODO
        options = {
            xaxis : {
                mode : 'time',
                labelsAngle : 0,
                min: start,
                max: stop,
                minorTickFreq: 4
            },
            yaxis: {
                autoscale : true,
                autoscaleMargin: 2,
                min: null,
                max: null
            },
            mouse: {
                track: true,		// => true to track the mouse, no tracking otherwise
                position: 'se',		// => position of the value box (default south-east)
                trackFormatter: function(obj){
                    var format = 'Avg. Price = {price}';
                    return format.replace("{price}",obj.y);
                },
                margin: 15,		// => margin in pixels of the valuebox
                color: '#264775',	// => line color of points that are drawn when mouse comes near a value of a series
                trackDecimals: 2,	// => decimals for the track values
                sensibility: 2,		// => the lower this number, the more precise you have to aim to show a value
                radius: 3		// => radius of the track point
            },
            points: {
                show: true,		// => setting to true will show points, false will hide
                radius: 3,		// => point radius (pixels)
                lineWidth: 2,		// => line width in pixels
                fill: true,		// => true to fill the points with a color, false for (transparent) no fill
                fillColor: '#ffffff'	// => fill color
            },
            lines: {
                show: true,		// => setting to true will show lines, false will hide
                color: '#264775',
                lineWidth: 2, 		// => line width in pixels
                fill: false,		// => true to fill the area from the line to the x axis, false for (transparent) no fill
                fillColor: null		// => fill color
            },
            grid: {
                minorVerticalLines: true
            },
            HtmlText : false
        };

        // Draw graph with default options, overwriting with passed options
        function drawGraph (opts) {

            // Clone the options, so the 'options' variable always keeps intact.
            o = Flotr._.extend(Flotr._.clone(options), opts || {});

            // Return a new graph.
            return Flotr.draw(
                container,
                [ d1 ],
                o
            );
        }
        graph = drawGraph();
    });
}

function barsDraw()
{
    var Params = graphParams.params;
    Params.bars = '1';
    Ajax.exec("api/graph", Params, function(result)
    {
        var d1        = result.ticks1,
            container = document.getElementById("graphicRender"),
            start     = result.ticks1[0][0],
            stop      = result.ticks1[(result.ticks1.length-1)][0],
            options, graph, x, o,
            context   = this;

        options = {
            xaxis : {
                mode : 'time',
                labelsAngle : 0
            },
            yaxis: {
                autoscale : true,
                autoscaleMargin: 2,
                min: null,
                max: null
            },
            candles  : {
                show : true,
                candleWidth : 20,
                fill: false,
                wickLineWidth: 20,
                upFillColor: '#029976',
                downFillColor: '#FF0000',
                fillOpacity: 1,
                barcharts: true
            },
            grid: {
                minorVerticalLines: true
            },
            HtmlText : false
        };

        // Draw graph with default options, overwriting with passed options
        function drawGraph (opts) {

            // Clone the options, so the 'options' variable always keeps intact.
            o = Flotr._.extend(Flotr._.clone(options), opts || {});

            // Return a new graph.
            return Flotr.draw(
                container,
                [ d1 ],
                o
            );
        }
        graph = drawGraph();
    });
}

function graphready()
{
        graphDraw();
        $("#barsMe").click(function(){
            barsready();
        });

        $(window).resize(function(){
            graphDraw();
        });
}

function barsready()
{
        barsDraw();
        $("#barsMe").click(function(){
            graphDraw();
            $("#barsMe").click(function(){
                barsready();
            });
        });
}