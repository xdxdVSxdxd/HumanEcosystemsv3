var researches;

//put here the researches for which you want the dashboard
var reserchesstring;

// can be day, week, month or all
var mode = "day";


var limit = 20000;

var language = "XXX";


$( document ).ready(function() {
 
 	reserchesstring = getUrlParameter("researches");
    mode = getUrlParameter("mode");
    if(!mode){
    	mode = "day";
    }
    researches = reserchesstring.split(",");

    if(getUrlParameter("limit")!=null){
        limit = getUrlParameter("limit");
    }

    if(getUrlParameter("language")!=null){
        language = getUrlParameter("language");
    }


    $("#modality").val(mode);

   	// prepare your javascript environment here
   	// now that the document has loaded
   
   	// .. and then start to create your visualisation
    doSomething();

    $("#modality").change(function(){
        mode = $("#modality").val();
        document.location = "./getviz?which=questions&researches=" + reserchesstring + "&mode=" + mode;
    });

    $("#q1").click( function(){   q1();  } );

});


function doSomething(){

	
}

// Q1
var Q1Data;
var Q1names = ["elettronica","rock","house","contemporanea","blues","italiana","classica"];
var Q1counter = 0;

function q1(){
    Q1data = null;
    Q1data = [];
    Q1counter = 0;

    $.each(Q1names, function (i, name) {

        $.getJSON("../api/getKeywordSeries", { 
            "researches" : reserchesstring , 
            "limit" : limit, 
            "mode" : mode, 
            "keyword" : name
        } , function(data){


            for(var k = 0; k<data.results.length; k++){
                data.results[k][0] = parseFloat( data.results[k][0] ) * 1000;
                data.results[k][1] = parseFloat( data.results[k][1] );
            }

            Q1data[i] = {
                name: name,
                data: data.results
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            Q1counter += 1;

            if (Q1counter === Q1names.length) {
                Q1createChart();
            }
        });
    });
}

function Q1createChart(){
    
    $("#results").html("");

    Highcharts.stockChart('results', {

            rangeSelector: {
                enabled: false
            },

            yAxis: {
                labels: {
                    formatter: function () {
                        return this.value ;
                    }
                },
                plotLines: [{
                    value: 0,
                    width: 2,
                    color: 'silver'
                }]
            },

            plotOptions: {
                series: {
                    compare: 'value',
                    connectNulls: true,
                    showInNavigator: true
                }
            },

            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
                valueDecimals: 2,
                split: true
            },

            series: Q1data
        });
}
// Q1 End


function exportdata(){

	// this function is called in the HTML template
	//
	// it handles the export of data from the visualisation
	// 
	// in the examples, you can see that the visualisations use
	// the endpoints of the Human Ecosystems API to get the data
	// to be visualised
	//
	// for example this one:
	// api/getEnergyComfortDistribution
	// which allows to get the emotional distribution of your research
	//
	// for example, to allow people to download the data, you might
	// imagine placing a link which opens up a window with the data in
	// it, without visualising it, as raw output from the API
	//
    // for example like this:
    //window.open("../api/getEnergyComfortDistribution?researches=" + reserchesstring );

}


// this is a simple utility to get parameters off the URL
// from the query string: you can use it to configure your visualisation
function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}


function hslToRgb(h, s, l){
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        var hue2rgb = function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
}

