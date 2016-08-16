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

    window.open("../api/getGeoPoints?researches=" + reserchesstring );

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
    heatmaps["all"] = new google.maps.visualization.HeatmapLayer({ map: map });
    heatmaps["all"].setOptions( {radius: 10, opacity: 0.7, dissipating: true });



    $.getJSON("../api/getGeoPoints", { "researches" : reserchesstring , "limit" : limit , "language": language})
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