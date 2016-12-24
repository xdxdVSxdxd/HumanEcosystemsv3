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
        document.location = "./getviz?which=dashboard&researches=" + reserchesstring + "&mode=" + mode;
    });    

});


function doSomething(){

    getStatistics();

    getSentiment();

    getSentimentTimeline();

    getEmotionsTimeline();

    getActivity();

    getMap();

    getEmotionsMap();

    getTopUsers();
	
}


function getStatistics(){

    $.getJSON("../api/getStatistics", 
      { 
        "researches" : reserchesstring,
        "mode": mode
      }
    )
    .done(function(data){

        //console.log(data);
        var nusers = data.results.nusers;
        var ncontents = data.results.ncontents;

        var c = "<strong>Number of users</strong>:" + nusers + "<br />" +
                "<strong>Number of content elements</strong>:" + ncontents;

        $("#stats").html( c ); 

    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });
}



function getSentiment(){

    
    $.getJSON("../api/getSentiment", { "researches" : reserchesstring , "limit" : limit, "mode" : mode })
    .done(function(data){

        var positive = +data.positive;
        var negative = +data.negative;
        var neutral = +data.neutral;
            
        var total = positive + neutral + negative;

        var perc_positive = 0;
        var perc_negative = 0;
        var perc_neutral = 0;  

        if(total!=0){
            perc_positive = 100*positive/total;
            perc_negative = 100*negative/total;
            perc_neutral = 100*neutral/total;
        }

        $("#positive").text(   perc_positive.toFixed(2)  + "%");
        $("#neutral").text(   perc_neutral.toFixed(2)  + "%");
        $("#negative").text(   perc_negative.toFixed(2)  + "%");
        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });

}


// sentimentseries
var sentimentseriesOptions = [],
    sentimentseriesCounter = 0,
    sentimentnames = ['positive', 'negative', 'neutral'];

function getSentimentTimeline(){
    

    $.each(sentimentnames, function (i, name) {

        $.getJSON("../api/getSentimentSeries", { "researches" : reserchesstring , "limit" : limit, "mode" : mode, "sentiment" : name.toLowerCase() } , function(data){


            for(var k = 0; k<data.results.length; k++){
                data.results[k][0] = parseFloat( data.results[k][0] ) * 1000;
                data.results[k][1] = parseFloat( data.results[k][1] );
            }

            sentimentseriesOptions[i] = {
                name: name,
                data: data.results
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            sentimentseriesCounter += 1;

            if (sentimentseriesCounter === sentimentnames.length) {
                sentimentseriescreateChart();
            }
        });
    });
}
function sentimentseriescreateChart(){
    Highcharts.stockChart('sentiment-time', {

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

            series: sentimentseriesOptions
        });
}
// sentimentseries end




// emotionstimeline
var emotionsOptions = [],
    emotionsCounter = 0,
    emotionsnames = ["Surprise","Interest","Arousal","Amusement","Proudness","Confidence","Satisfaction","Happiness","Pleasure","Content","Comfort","Sympathy","Tranquillity","Relax","Calm","Lethargy","Boredom","Depression","Sadness","Guilt","Miserability","Displeasure","Disgust","Anger","Fear","Embarassment","Contempt","Confusion","Affliction","Restlessness","Disturbance","Agitation","Terror","Nervous","Tension","Pain"];

function getEmotionsTimeline(){
    

    $.each(emotionsnames, function (i, name) {

        $.getJSON("../api/getEmotionsSeries", { "researches" : reserchesstring , "limit" : limit, "mode" : mode, "emotion" : name } , function(data){


            for(var k = 0; k<data.results.length; k++){
                data.results[k][0] = parseFloat( data.results[k][0] ) * 1000;
                data.results[k][1] = parseFloat( data.results[k][1] );
            }

            emotionsOptions[i] = {
                name: name,
                data: data.results
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            emotionsCounter += 1;

            if (emotionsCounter === emotionsnames.length) {
                emotionscreateChart();
            }
        });
    });
}
function emotionscreateChart(){
    Highcharts.stockChart('emotions', {

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

            series: emotionsOptions
        });
}
// emotionstimeline end







function getActivity(){

    
    $.getJSON("../api/getActivity", { "researches" : reserchesstring , "mode" : mode })
    .done(function(data){


        Highcharts.chart('activity', {

        chart: {
            type: 'bubble',
            plotBorderWidth: 1,
            zoomType: 'xy'
        },

        legend: {
            enabled: false
        },

        title: {
            text: 'Activity per hour of the day'
        },

        
        xAxis: {
            gridLineWidth: 1,
            title: {
                text: 'Hour of day'
            },
            labels: {
                format: '{value}'
            }
        },

        yAxis: {
            startOnTick: false,
            endOnTick: false,
            title: {
                text: 'Number of messages'
            },
            labels: {
                format: '{value}'
            },
            maxPadding: 0.2
        },

        tooltip: {
            useHTML: true,
            headerFormat: '<table>',
            pointFormat: '<tr><th colspan="2"><h3>{point.label} h</h3></th></tr>' +
                '<tr><th>Messages:</th><td>{point.y} messages</td></tr>',
            footerFormat: '</table>',
            followPointer: true
        },

        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                }
            }
        },

        series: [data]

    });


    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });

}



var styles = [
      {
        "stylers": [
          { "visibility": "off" }
        ]
      },{
        "featureType": "administrative",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" }
        ]
      },{
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#f2f2f2" }
        ]
      },{
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#d5d5d5" }
        ]
      },{
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#aaaaaa" }
        ]
      },{
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#AAAAAA" }
        ]
      },{
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#AAAAFF" }
        ]
      }
    ];




// getMap
var map;
var heatmaps;
var legend;
var markers;
function getMap(){

    var styledMap = new google.maps.StyledMapType(styles, {name: "HEMap"});

    markers = new Array();

    var mapOptions = {
        center: new google.maps.LatLng(45.466667, 9.183333),
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        backgroundColor: '#FFFFFF',
        mapTypeControl: false,
        panControl: false,
        rotateControl: false,
        scaleControl: false,
        scrollwheel: false,
        streetViewControl: false,
        zoomControl: true
    };

    map = new google.maps.Map(document.getElementById("map"), mapOptions);

    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');

    legend = new Object();
    legend["all"] = "#00FF00";

    heatmaps = new Object();
    heatmaps["all"] = new google.maps.visualization.HeatmapLayer({ map: map });
    heatmaps["all"].setOptions( {radius: 20, opacity: 0.7, dissipating: true });

    $.getJSON("../api/getGeoPoints", { "researches" : reserchesstring , "limit" : limit , "language": language, "mode" : mode })
    .done(function(data){

                var datas = new Object();
                datas["all"] = new google.maps.MVCArray();

                for(var i = 0; i<data.results.length; i++){
                  datas[  "all"  ].push(  { location: new google.maps.LatLng(  parseFloat(data.results[i].lat) , parseFloat(data.results[i].lng)   ) , weight: parseFloat(data.results[i].c) } );

                  
                  /*

                    here is if you want to add some markers

                  var marker = new google.maps.Marker({
                    position: {lat: parseFloat(data.results[i].lat), lng: parseFloat(data.results[i].lng)},
                    map: map,
                    title: 'i'
                  });

                    */

                    
                }

                heatmaps["all"].set('data' , datas["all"]);

        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });

}
// getMap end





//getEmotionsMap()
var emotionmap;
var emotionheatmaps;
var emotionlegend;
var emotionmarkers;
function getEmotionsMap(){
    var styledMap = new google.maps.StyledMapType(styles, {name: "HEMap"});

    emotionmarkers = new Array();

    var mapOptions = {
        center: new google.maps.LatLng(45.466667, 9.183333),
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        backgroundColor: '#FFFFFF',
        mapTypeControl: false,
        panControl: false,
        rotateControl: false,
        scaleControl: false,
        scrollwheel: false,
        streetViewControl: false,
        zoomControl: true
    };

    emotionmap = new google.maps.Map(document.getElementById("emotions-map"), mapOptions);

    emotionmap.mapTypes.set('map_style', styledMap);
    emotionmap.setMapTypeId('map_style');

    emotionlegend = new Array();

    emotionheatmaps = new Object();
    

    var detectedEmotions = new Array();

    $.getJSON("../api/getGeoEmotionPoints", { "researches" : reserchesstring , "limit" : limit , "language": language, "mode" : mode })
    .done(function(data){

                //console.log(data);

                data.results.forEach(function(d){

                  var found = false;
                  for(var i = 0; i<detectedEmotions.length && !found; i++){
                    if(detectedEmotions[i]==d.label){
                      found = true;
                    }
                  }
                  if(!found){
                    detectedEmotions.push(d.label);
                  }

                });

                //console.log(detectedEmotions);

                var datas = new Object();


                var huestep = 1/(detectedEmotions.length+1);

                var i = 0;

                detectedEmotions.forEach(function(d){
                  datas[d] = new google.maps.MVCArray();
                  emotionheatmaps[d] = new google.maps.visualization.HeatmapLayer({ map: emotionmap });
                  emotionheatmaps[d].setOptions( {radius: 20, opacity: 0.7, dissipating: true });

                  var hue = i*huestep;

                  
                  var gradient = new Array();
                  for(var j = 0; j<1; j=j+0.1){
                    var rgb = hslToRgb(hue,1,j);
                    gradient.push(  'rgba(' +  rgb[0] + ',' +  rgb[1]  + ',' + rgb[2] + ',' +  (j==0?0:1)  + ')'  );
                  }

                  //console.log(gradient);

                  var rgb = hslToRgb(hue,1,0.5);

                  var o = new Object();
                  o.label = d;
                  o.color = 'rgba(' +  rgb[0] + ',' +  rgb[1]  + ',' + rgb[2] + ',' +  (j==0?0:1)  + ')';

                  emotionlegend.push(o);

                  emotionheatmaps[d].set('gradient',  gradient);

                  i++;

                });


                

                for(var i = 0; i<data.results.length; i++){
                  datas[  data.results[i].label  ].push(  { location: new google.maps.LatLng(  parseFloat(data.results[i].lat) , parseFloat(data.results[i].lng)   ) , weight: parseFloat(data.results[i].c) } );

                  
                  /*

                    here is if you want to add some markers

                  var marker = new google.maps.Marker({
                    position: {lat: parseFloat(data.results[i].lat), lng: parseFloat(data.results[i].lng)},
                    map: map,
                    title: 'i'
                  });

                    */

                    
                }

                detectedEmotions.forEach(function(d){
                  emotionheatmaps[d].set('data' , datas[d]);
                });

                var legendhtml = "";

                //console.log(emotionlegend);

                for( var k =0 ; k<emotionlegend.length; k++){

                    //console.log(emotionlegend[k]);

                    legendhtml = legendhtml + "<div class='legenditemholder'>";
                    legendhtml = legendhtml + "     <div class='legenditemcolor' style='background:" + emotionlegend[k].color + ";'></div>";
                    legendhtml = legendhtml + "     <div class='legenditemlabel'>" + emotionlegend[k].label + "</div>";
                    legendhtml = legendhtml + "</div>";
                }

                $("#emotions-map-legend").html(legendhtml);
        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });
}
//getEmotionsMap() end

//getTopUsers()
function getTopUsers(){
    $.getJSON("../api/getTopUsers", { "researches" : reserchesstring , "mode" : mode })
    .done(function(data){
        console.log(data.results);

        var htmlstring = "<div class='table-responsive'><table class='table-striped topuserstable'>";

        htmlstring = htmlstring + " <tr><th></th><th>name</th><th>nick</th><th>posts</th><th>reach</th><th>comfort</th><th>energy</th><th>link</th></tr>";

        for(var i = 0; i<data.results.length; i++){

            htmlstring = htmlstring + " <tr>";
            htmlstring = htmlstring + "     <td><img src='" + data.results[i].profile_image_url + "' class='topuserimage' /></td>";
            htmlstring = htmlstring + "     <td>" + data.results[i].name + "</td>";
            htmlstring = htmlstring + "     <td>" + data.results[i].screen_name + "</td>";
            htmlstring = htmlstring + "     <td>" + data.results[i].c + "</td>";
            htmlstring = htmlstring + "     <td>" + data.results[i].coeff + "</td>";
            htmlstring = htmlstring + "     <td>" + data.results[i].avgcomfort + "</td>";
            htmlstring = htmlstring + "     <td>" + data.results[i].avgenergy + "</td>";
            htmlstring = htmlstring + "     <td><a target='_blank' href='" + data.results[i].profile_url + "'><span class='glyphicon glyphicon-link'></span> Link</a></td>";
            htmlstring = htmlstring + " </tr>";
            
        }

        htmlstring = htmlstring + "</table></div>";
 

        $("#topusers").html( htmlstring );

    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });
}
//getTopUsers() end


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