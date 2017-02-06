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
    $("#q2").click( function(){   q2();  } );
    $("#q3").click( function(){   q3();  } );
    $("#q4").click( function(){   q4();  } );
    $("#q5").click( function(){   q5();  } );

});


function doExport(url,label){
    var cont = "<a href='" + url + "' target='_blank' class='btn btn-primary'>Export " + label + "</a>";
    $("#export-div").append(cont);
}


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

    $("#export-div").html("");

    $("#explanation").html("<strong>Generi Musicali:</strong> calcolato tramite il conteggio delle mention sui vari generi musicali.");

    $.each(Q1names, function (i, name) {

        $.getJSON("../api/getKeywordSeries", { 
            "researches" : reserchesstring , 
            "limit" : limit, 
            "mode" : mode, 
            "keyword" : name
        } , function(data){

            var urlo = "../api/getKeywordSeries?researches=" + reserchesstring + "&limit=" + limit + "&mode=" + mode + "&keyword=" + name;
            doExport(urlo,name);

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





// Q2
var Q2Data;
var Q2names = ["desire"];
var Q2counter = 0;

function q2(){
    Q2data = null;
    Q2data = [];
    Q2counter = 0;

    $("#export-div").html("");

    $("#explanation").html("<strong>Desiderio di musica live:</strong> calcolato tramite il conteggio delle espressioni emozionali che maggiormente indicano desiderio (curiosità, aspettativa, sorpresa...).");

    $.each(Q2names, function (i, name) {

        $.getJSON("../api/getEmotionalBoundariesSeries", { 
            "researches" : reserchesstring , 
            "limit" : limit, 
            "mode" : mode,
            "emotion-condition" : "comfort>100 AND energy>100"
        } , function(data){

            var urlo = "../api/getEmotionalBoundariesSeries?researches=" + reserchesstring + "&limit=" + limit + "&mode=" + mode + "&emotion-condition=comfort>100 AND energy>100";
            doExport(urlo,"");

            for(var k = 0; k<data.results.length; k++){
                data.results[k][0] = parseFloat( data.results[k][0] ) * 1000;
                data.results[k][1] = parseFloat( data.results[k][1] );
            }

            Q2data[i] = {
                name: name,
                data: data.results
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            Q2counter += 1;

            if (Q2counter === Q2names.length) {
                Q2createChart();
            }
        });
    });
}

function Q2createChart(){
    
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

            series: Q2data
        });
}
// Q2 End







// Q3
var Q3Data;
var Q3names = [
                    "scala,auditorium,assago,magnolia,san siro,santeria,stadio",
                    "BasementMilano,IDistratti,LeCannibale,lecannibaleclub,nul3000,FabriqueMilano,hollywoodmilano,JustCavalliClub,cavalli_milan,leoncavallospa,circolomagnolia,oldfashionclub,SanteriaMilano,tocqueville",
                    "base_milano,avanzi,mare culturale"
            ];
var Q3Labels = ["Grandi venue","Piccole Venue","Centri Culturali"]
var Q3counter = 0;

function q3(){
    Q3data = null;
    Q3data = [];
    Q3counter = 0;

    $("#export-div").html("");

    $("#explanation").html("<strong>Grandi vs Piccole venue:</strong> si confrontano le grandi venue, le venue di dimensioni minori e i centri culturali integrati. Il confronto avviene conteggiando le mention, pesate per un coefficiente proporzionale al successo dei messaggi che le contengono (ovvero proporzionale alla positività del contenuto e al numero delle condivisioni).");

    $.each(Q3names, function (i, name) {

        $.getJSON("../api/getMultipleMentionsSeries", { 
            "researches" : reserchesstring , 
            "limit" : limit, 
            "mode" : mode,
            "mentions" : name
            //"weightwith" : "favorite_count"
        } , function(data){

            var urlo = "../api/getMultipleMentionsSeries?researches=" + reserchesstring + "&limit=" + limit + "&mode=" + mode + "&mentions=" + name;
            doExport(urlo,Q3Labels[i]);

            for(var k = 0; k<data.results.length; k++){
                data.results[k][0] = parseFloat( data.results[k][0] ) * 1000;
                data.results[k][1] = parseFloat( data.results[k][1] );
            }

            Q3data[i] = {
                name: Q3Labels[i],
                data: data.results
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            Q3counter += 1;

            if (Q3counter === Q3names.length) {
                Q3createChart();
            }
        });
    });
}

function Q3createChart(){
    
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

            series: Q3data
        });
}
// Q3 End



// Q4
var Q4Data;
var Q4labels = ["Clubbing"];
var Q4names = ["discoteca,club,dj,djset,house,elettronica,rave"];
var Q4counter = 0;

function q4(){
    Q4data = null;
    Q4data = [];
    Q4counter = 0;

    $("#export-div").html("");

    $("#explanation").html("<strong>Clubbing:</strong> calcolato misurando le menzioni degli elementi rilevanti per il clubbing (discoteca, djset, rave, house, elettronica,...) e pesandoli in maniera proporzionale ai livelli di energia e comfort.");

    $.each(Q4names, function (i, name) {

        $.getJSON("../api/getMultipleMentionsSeries", { 
            "researches" : reserchesstring , 
            "limit" : limit, 
            "mode" : mode, 
            "mentions" : name,
            "weightwith" : "(1+(1+comfort)/(1+energy))"
        } , function(data){

            var urlo = "../api/getMultipleMentionsSeries?researches=" + reserchesstring + "&limit=" + limit + "&mode=" + mode + "&mentions=" + name + "&weightwith=(1+(1+comfort)/(1+energy))";
            doExport(urlo,Q4labels[i]);

            for(var k = 0; k<data.results.length; k++){
                data.results[k][0] = parseFloat( data.results[k][0] ) * 1000;
                data.results[k][1] = parseFloat( data.results[k][1] );
            }

            Q4data[i] = {
                name: Q4labels[i],
                data: data.results
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            Q4counter += 1;

            if (Q4counter === Q4names.length) {
                Q4createChart();
            }
        });
    });
}

function Q4createChart(){
    
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

            series: Q4data
        });
}
// Q4 End





// Q5
function q5(){
    $("#explanation").html("<strong>Destinazioni:</strong> Calcolato conteggiando i contenuti e le condivisioni di rilevanza geografica (quelle con coordinate o con forme linguistiche che indicano spostamento o permanenza sul territorio), e suddividendole tra i vari distretti.");
    $("#results").html("");
    $("#results").css("height", "500px");

    var map = new google.maps.Map(document.getElementById('results'), {
      zoom: 11,
      center: {lat: 45.466667, lng: 9.183333}
    });

    var ctaLayer = new google.maps.KmlLayer({
      url: 'http://164.132.225.138/~hebase/Censimento.kmz',
      map: map
    });

    console.log(ctaLayer);
 
}
// Q4 End





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

