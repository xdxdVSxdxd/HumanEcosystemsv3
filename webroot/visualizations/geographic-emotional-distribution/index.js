var researches;
var reserchesstring;


var limit = 500;

var language = "XXX";

$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    if(getUrlParameter("limit")!=null){
        limit = getUrlParameter("limit");
    }

    if(getUrlParameter("language")!=null){
        language = getUrlParameter("language");
    }


    getGeoDistribution();

});


    var map;

    var heatmaps;

    var legend;

    var markers;



function exportdata(){

    window.open("../api/getGeoEmotionPoints?researches=" + reserchesstring );

}


function getGeoDistribution(){

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
          { "color": "#000000" }
        ]
      },{
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#222222" }
        ]
      },{
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#444444" }
        ]
      },{
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#333333" }
        ]
      },{
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#000055" }
        ]
      }
    ];

    var styledMap = new google.maps.StyledMapType(styles, {name: "HEMap"});

    markers = new Array();

    var mapOptions = {
        center: new google.maps.LatLng(0, 0),
        zoom: 3,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        backgroundColor: '#EEEEEE',
        mapTypeControl: false,
        panControl: false,
        rotateControl: false,
        scaleControl: false,
        scrollwheel: true,
        streetViewControl: false,
        zoomControl: false
    };

    map = new google.maps.Map(document.getElementById("mapholder"), mapOptions);

    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');

    legend = new Object();
    legend["all"] = "#00FF00";

    heatmaps = new Object();
    

    var detectedEmotions = new Array();

    $.getJSON("../api/getGeoEmotionPoints", { "researches" : reserchesstring , "limit" : limit , "language": language})
    .done(function(data){

                console.log(data);

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

                console.log(detectedEmotions);

                var datas = new Object();


                var huestep = 1/(detectedEmotions.length+1);

                var i = 0;

                detectedEmotions.forEach(function(d){
                  datas[d] = new google.maps.MVCArray();
                  heatmaps[d] = new google.maps.visualization.HeatmapLayer({ map: map });
                  heatmaps[d].setOptions( {radius: 10, opacity: 0.7, dissipating: true });

                  var hue = i*huestep;

                  
                  var gradient = new Array();
                  for(var j = 0; j<1; j=j+0.1){
                    var rgb = hslToRgb(hue,1,j);
                    gradient.push(  'rgba(' +  rgb[0] + ',' +  rgb[1]  + ',' + rgb[2] + ',' +  (j==0?0:1)  + ')'  );
                  }

                  console.log(gradient);

                  heatmaps[d].set('gradient',  gradient);

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
                  heatmaps[d].set('data' , datas[d]);
                });

        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });

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