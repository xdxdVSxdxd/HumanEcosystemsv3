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

    getTopics(true);

    //getTopicRelations(true);

    //getUserRelations(true);
	
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
                "<strong>Number of content elements</strong>:" + ncontents + "<br />" +
                "<a class='btn btn-default btn-xs' href='../api/getStatistics?researches=" + reserchesstring + "&mode=" + mode + "' target='_blank'>Export</a>";

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
    
    var cont = "";

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

            cont = cont + "<a class='btn btn-default btn-xs' href='../api/getSentimentSeries?researches=" + reserchesstring + "&limit=" + limit + "&mode=" + mode + "&sentiment=" + name.toLowerCase() + "' target='_blank'>Export " + name.toLowerCase() + " timeseries</a> ";

            if (sentimentseriesCounter === sentimentnames.length) {
                sentimentseriescreateChart();

                $("#sentiment-export").html(cont);
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
    
    var cont = "";

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

            cont = cont + "<a class='btn btn-default btn-xs' href='../api/getEmotionsSeries?researches=" + reserchesstring + "&limit=" + limit + "&mode=" + mode + "&emotion=" + name + "' target='_blank'>Export " + name + " timeseries</a> ";

            if (emotionsCounter === emotionsnames.length) {
                emotionscreateChart();

                $("#emotions-export").html(cont);
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


        var cont = "<a href='../api/getActivity?researches=" + reserchesstring + "&mode=" + mode + "' target='_blank' class='btn btn-default btn-xs'>Export</a>"
        $("#activity-export").html(cont);
        
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

                var cont = "<a href='../api/getGeoPoints?researches=" + reserchesstring + "&limit=" + limit + "&language=" + language + "&mode=" + mode + "' target='_blank' class='btn btn-default btn-xs'>Export</a>";
                $("#map-export").html(cont);

        
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

                var cont = "<a href='../api/getGeoEmotionPoints?researches=" + reserchesstring + "&limit=" + limit + "&language=" + language + "&mode=" + mode + "' target='_blank' class='btn btn-default btn-xs'>Export</a>";
                $("#emotions-map-export").html(cont);
        
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
        //console.log(data.results);

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

        var cont = "<a href='../api/getTopUsers?researches=" + reserchesstring + "&mode=" + mode + "' target='_blank' class='btn btn-default btn-xs'>Export</a>";
        $("#topusers-export").html(cont);

    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });
}
//getTopUsers() end



//getTopics()
function getTopics( clearGraph ){
    var tcbleed, tcwidth, tcheight;
        var tcpack;
        var tcsvg;

        tcbleed = 100;
        tcwidth = $("#topics").width();
        tcheight = 800;

            tcpack = d3.layout.pack()
            .sort(null)
            .size([tcwidth, tcheight + tcbleed * 2])
            .padding(2);

            tcsvg = d3.select("#topics").append("svg")
            .attr("width", tcwidth)
            .attr("height", tcheight)
            .append("g")
            .attr("transform", "translate(0," + -tcbleed + ")");


    $.getJSON("../api/getHashtagCloud", { "researches" : reserchesstring , "limit" : limit , "language": language, "mode" : mode})
    .done(function(data){

        //console.log(data);


              var node = tcsvg.selectAll(".node")
                .data(tcpack.nodes(data)
                .filter(function(d) { return !d.children; }))
                .enter().append("g")
                .attr("class", "node")
                .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

              node.append("circle")
                .attr("class" , "tccircle")
                .attr("r", function(d) { return d.r; });

              node.append("text")
                .text(function(d) { return d.name; })
                .attr("class" , "tctext")
                .style("font-size", function(d) { var vv = (2 * d.r - 8) / this.getComputedTextLength() * 24; if(vv<0){vv=0;} var v = Math.min(2 * d.r, vv); if(v<0){v=0;} return v + "px"; })
                .attr("dy", ".35em");




                var cont = "<a href='../api/getHashtagCloud?researches=" + reserchesstring + "&limit=" + (limit*2) +  "&language=" + language + "&mode=" + mode + "' target='_blank' class='btn btn-default btn-xs'>Export</a>";
                $("#topics-export").html(cont);

        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });
}
//getTopics() end



//getTopicRelations()
var wgwidth, wgheight;
var wgcolor;
var wgforce;
var wgsvg1,wgsvg;
var maxn = 1;
var radius = 20;
function redrawwords() {
    //console.log("here", d3.event.translate, d3.event.scale);
    wgsvg.attr("transform",
        "translate(" + d3.event.translate + ")"
        + " scale(" + d3.event.scale + ")");
}
function exportTopicRelations(){
    window.open('../api/getHashtagNetwork?researches=' + reserchesstring + '&limit=' + 3000 + '&mode=' + mode);
}
function getTopicRelations(clearGraph){
        $("#topic-relations").html("");
        wgwidth = $("#topic-relations").width();
        wgheight = 900;
        $("#topic-relations").height( wgheight );

        wgcolor = d3.scale.category20();

        wgforce = d3.layout.force()
          .linkDistance(40)
          .linkStrength(0.5)
          .charge(-90)
          .size([wgwidth, wgheight]);

        wgforce.drag()
          .on("dragstart", function() { d3.event.sourceEvent.stopPropagation(); });

        wgsvg1 = d3.select("#topic-relations").append("svg")
          .attr("width", wgwidth)
          .attr("height", wgheight)
          .attr("pointer-events", "all");

        wgsvg = wgsvg1
          .append('svg:g')
          .call(d3.behavior.zoom().on("zoom", redrawwords))
          .append('svg:g');


          wgsvg
          .append('svg:rect')
          .attr('x', -10000)
          .attr('y', -10000)
          .attr('width', 20000)
          .attr('height', 20000)
          .attr('fill', 'white');


        /*

        Remove?

        var numero = Math.min(8,wordstats.length);
        var idstring = "";

        for(var i = 0; i<numero; i++){
          idstring = idstring + wordstats[i].id;
          if(i<(numero-1)){
            idstring = idstring + ",";
          }
        }
        */

        //
        $.getJSON("../api/getHashtagNetwork", { "researches" : reserchesstring ,  "mode" : mode, "limit" : 500 })
        .done(function(data){

            var graph = data;

            var nodes = graph.nodes.slice(),
              links = [],
              bilinks = [];

            maxn = 1;

            
            graph.links.forEach(function(link) {

                var founds = false;
                var foundt = false;
                for(var k = 0; k<nodes.length && (!founds || !foundt); k++){
                    if(nodes[k].label==link.source){
                        founds = true;
                        link.source = k;
                    }
                    if(nodes[k].label==link.target){
                        foundt = true;
                        link.target = k;
                    }
                }

              var s = nodes[link.source],
                  t = nodes[link.target],
                  i = {n: 1, weight: 1}; // intermediate node
              nodes.push(i);
              links.push({source: s, target: i}, {source: i, target: t});
              bilinks.push([s, i, t]);
            });


            nodes.forEach(function(node){
              node.c = +node.weight;
              if(node.c>maxn){ maxn = node.c; }
            });

            
            wgforce
              .nodes(nodes)
              .links(links)
              .start();

            
            var link = wgsvg.selectAll(".link")
              .data(bilinks)
              .enter().append("path")
              .attr("class", "link");

            var node = wgsvg.selectAll(".node")
              .data(graph.nodes);


            var nodeEnter = node
                            .enter()
                            .append("svg:g")
                            .attr("class", "node")
                            .call(wgforce.drag);

            wgsvg.selectAll(".node")
              .on("click",function(d){
                if (d3.event.shiftKey) {
                  document.location = "dosomething?iinput=" + d.word + "&w=" + project;
                }
              });

            nodeEnter.append("circle")
              .attr("r", function(d){ return 4+(100*d.c/maxn); });
              //.style("fill", function(d) { return wgcolor(d.n); });

            var wgtexts = nodeEnter.append("svg:text")
              .attr("class", "nodetext")
              .attr("dx", function(d){  return 12; })
              .attr("dy", ".35em")
              .style("font-size",function(d){ return 4 + (40*d.c/maxn); })
              .text(function(d) { return d.label });

            node.append("title")
              .text(function(d) { return d.label; });

            wgforce.on("tick", function() {

              link.attr("d", function(d) {
                return "M" + d[0].x + "," + d[0].y + "S" + d[1].x + "," + d[1].y + " " + d[2].x + "," + d[2].y;
              });
              
              

              node.attr("transform", function(d) {

                var ddx = d.x;
                var ddy = d.y;
                //if(ddx<0){ddx=0;} else if(ddx>wgwidth){ddx=wgwidth;}
                //if(ddy<0){ddy=0;} else if(ddy>wgheight){ddy=wgheight;}

                return "translate(" + ddx + "," + ddy + ")";
              });
              

              //node.attr("cx", function(d) { return d.x = Math.max(radius, Math.min( wgwidth - radius, d.x)); })
              //    .attr("cy", function(d) { return d.y = Math.max(radius, Math.min(wgheight - radius, d.y)); });

              link.attr("x1", function(d) { return d[0].x; })
                  .attr("y1", function(d) { return d[0].y; })
                  .attr("x2", function(d) { return d[1].x; })
                  .attr("y2", function(d) { return d[1].y; });

              wgtexts
                  .attr("dx", function(d) {   

                    val = 0;

                    if(d.x>wgwidth/2){
                      val = -12;// - d.n/4 - this.getComputedTextLength();
                    } else {
                      val = 12;// + d.n/4;
                    }

                    return val;

                  });


            });

        })
        .fail(function( jqxhr, textStatus, error ){
            //fare qualcosa in caso di fallimento
        });
        //
}
//getTopicRelations() end




//getUserRelations()
var ugwidth, ugheight;
var ugcolor;
var ugforce;
var ugsvg1,ugsvg;
var maxnu = 1;
function redrawusers() {
    //console.log("here", d3.event.translate, d3.event.scale);
    ugsvg.attr("transform",
        "translate(" + d3.event.translate + ")"
        + " scale(" + d3.event.scale + ")");
}
function exportUserRelations(){
    window.open('../api/getRelations?researches=' + reserchesstring + '&limit=' + 3000 + '&mode=' + mode);
}
function getUserRelations(clearGraph){
        $("#user-relations").html("");
        ugwidth = $("#user-relations").width();
        ugheight = 900;
        $("#user-relations").height( ugheight );

        ugcolor = d3.scale.category20();

        ugforce = d3.layout.force()
          .linkDistance(40)
          .linkStrength(0.5)
          .charge(-90)
          .size([ugwidth, ugheight]);

        ugforce.drag()
          .on("dragstart", function() { d3.event.sourceEvent.stopPropagation(); });

        ugsvg1 = d3.select("#user-relations").append("svg")
          .attr("width", ugwidth)
          .attr("height", ugheight)
          .attr("pointer-events", "all");

        ugsvg = ugsvg1
          .append('svg:g')
          .call(d3.behavior.zoom().on("zoom", redrawusers))
          .append('svg:g');


          ugsvg
          .append('svg:rect')
          .attr('x', -10000)
          .attr('y', -10000)
          .attr('width', 20000)
          .attr('height', 20000)
          .attr('fill', 'white');


        /*

        Remove?

        var numero = Math.min(8,wordstats.length);
        var idstring = "";

        for(var i = 0; i<numero; i++){
          idstring = idstring + wordstats[i].id;
          if(i<(numero-1)){
            idstring = idstring + ",";
          }
        }
        */

        //
        $.getJSON("../api/getRelations", { "researches" : reserchesstring ,  "mode" : mode, "limit" : 500 })
        .done(function(data){

            var graph = data;

            var nodes = graph.nodes.slice(),
              links = [],
              bilinks = [];

            maxnu = 1;

            
            graph.links.forEach(function(link) {

                var founds = false;
                var foundt = false;
                for(var k = 0; k<nodes.length && (!founds || !foundt); k++){
                    if(nodes[k].nick==link.source){
                        founds = true;
                        link.source = k;
                    }
                    if(nodes[k].nick==link.target){
                        foundt = true;
                        link.target = k;
                    }
                }

              var s = nodes[link.source],
                  t = nodes[link.target],
                  i = {n: 1, weight: 1}; // intermediate node
              nodes.push(i);
              links.push({source: s, target: i}, {source: i, target: t});
              bilinks.push([s, i, t]);
            });


            nodes.forEach(function(node){
              node.c = +node.weight;
              if(node.c>maxn){ maxn = node.c; }
            });

            
            ugforce
              .nodes(nodes)
              .links(links)
              .start();

            
            var link = ugsvg.selectAll(".link")
              .data(bilinks)
              .enter().append("path")
              .attr("class", "link");

            var node = ugsvg.selectAll(".node")
              .data(graph.nodes);


            var nodeEnter = node
                            .enter()
                            .append("svg:g")
                            .attr("class", "node")
                            .call(ugforce.drag);

            ugsvg.selectAll(".node")
              .on("click",function(d){
                if (d3.event.shiftKey) {
                  window.open(d.pu);
                }
              });

            nodeEnter.append("circle")
              .attr("r", function(d){ return 4+(100*d.c/maxn); });
              //.style("fill", function(d) { return wgcolor(d.n); });

            var ugtexts = nodeEnter.append("svg:text")
              .attr("class", "nodetext")
              .attr("dx", function(d){  return 12; })
              .attr("dy", ".35em")
              .style("font-size",function(d){ return 4 + (40*d.c/maxn); })
              .text(function(d) { return d.nick });

            node.append("title")
              .text(function(d) { return d.nick; });

            ugforce.on("tick", function() {

              link.attr("d", function(d) {
                return "M" + d[0].x + "," + d[0].y + "S" + d[1].x + "," + d[1].y + " " + d[2].x + "," + d[2].y;
              });
              
              

              node.attr("transform", function(d) {

                var ddx = d.x;
                var ddy = d.y;
                //if(ddx<0){ddx=0;} else if(ddx>wgwidth){ddx=wgwidth;}
                //if(ddy<0){ddy=0;} else if(ddy>wgheight){ddy=wgheight;}

                return "translate(" + ddx + "," + ddy + ")";
              });
              

              //node.attr("cx", function(d) { return d.x = Math.max(radius, Math.min( wgwidth - radius, d.x)); })
              //    .attr("cy", function(d) { return d.y = Math.max(radius, Math.min(wgheight - radius, d.y)); });

              link.attr("x1", function(d) { return d[0].x; })
                  .attr("y1", function(d) { return d[0].y; })
                  .attr("x2", function(d) { return d[1].x; })
                  .attr("y2", function(d) { return d[1].y; });

              ugtexts
                  .attr("dx", function(d) {   

                    val = 0;

                    if(d.x>wgwidth/2){
                      val = -12;// - d.n/4 - this.getComputedTextLength();
                    } else {
                      val = 12;// + d.n/4;
                    }

                    return val;

                  });


            });

        })
        .fail(function( jqxhr, textStatus, error ){
            //fare qualcosa in caso di fallimento
        });
        //
}
//getUserRelations() end



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

