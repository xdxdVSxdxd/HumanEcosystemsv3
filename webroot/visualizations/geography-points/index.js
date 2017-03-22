var researches;
var reserchesstring;

var JSONPolygons = ["Distretti.geojson"];
var JSONLayers = ["Arene-piazze-parchi.geojson", "teatri-sale-concerto.geojson", "venues.geojson"];

var c20 = d3.scale.category20();

var limit = 500;

var total = 0;
var totalPoly = 0;

var GEOJSONDATA,GEOJSONDATAPOLY;

var exportdata;

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

    GEOJSONDATA = new Array();
    GEOJSONDATAPOLY = new Array();

    exportdata = new Array();

    getGeoDistribution();

});


    var map;


    var markers;

    var polygons;


function exportalldata(){

    var content = "type,name,destinations,comfort,energy<br />";
    for(var i = 0; i<exportdata.length; i++){
      content = content + exportdata[i].type + "," + exportdata[i].name + "," + exportdata[i].destinations + "," + exportdata[i].comfort + "," + exportdata[i].energy + "<br />";
    }

    var win = window.open("", "Export", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=780, height=200, top="+(screen.height-400)+", left="+(screen.width-840));
    win.document.body.innerHTML = content;

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
          { "color": "#333333" }
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
          { "color": "#444444" }
        ]
      },{
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
          { "visibility": "on" },
          { "color": "#000022" }
        ]
      }
    ];

    var styledMap = new google.maps.StyledMapType(styles, {name: "HEMap"});

    markers = new Array();

    polygons = new Array();

    var mapOptions = {
        center: new google.maps.LatLng(45.466667, 9.183333),
        zoom: 11,
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


    for(var i = 0; i<JSONPolygons.length; i++){

      getPolygon(i);

    }


    for(var i = 0; i<JSONLayers.length; i++){

      getLayer(i);

    }


    /*
    $.getJSON("../api/getGeoPoints", { "researches" : reserchesstring , "limit" : limit , "language": language})
    .done(function(data){

                  
                  
                  var marker = new google.maps.Marker({
                    position: {lat: parseFloat(data.results[i].lat), lng: parseFloat(data.results[i].lng)},
                    map: map,
                    title: 'i'
                  });

                  
        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });
    */

}


var maxSizePoly = 0;
var howmanyPoly = 0;


function getPolygon(i){
  d3.json("../webroot/visualizations/geography-points/GeoJSON/" + JSONPolygons[i],function(error,data){
        if(!error){

          GEOJSONDATAPOLY[i]= data ;

          totalPoly = totalPoly + data.features.length;

          function createCallback(idx,jdx,tot) {
             return function(data2) {
                // use idx
                
                data2.results[0].c = +data2.results[0].c;
                data2.results[0].comfort = +data2.results[0].comfort;
                data2.results[0].energy = +data2.results[0].energy;

                exportdata.push(
                  {
                    type: "Area",
                    name: GEOJSONDATAPOLY[idx].features[jdx].properties.Name,
                    destinations: data2.results[0].c,
                    comfort: data2.results[0].comfort,
                    energy: data2.results[0].energy
                  }
                );

                var siz = 2 + data2.results[0].c;

                if(maxSizePoly<siz){ maxSizePoly = siz; }

                var coords = new Array();

                for(var k = 0; k<GEOJSONDATAPOLY[idx].features[jdx].geometry.coordinates.length; k++){
                  for(var kk = 0; kk<GEOJSONDATAPOLY[idx].features[jdx].geometry.coordinates[k].length; kk++){
                    coords.push(  
                        {
                          lat: GEOJSONDATAPOLY[idx].features[jdx].geometry.coordinates[k][kk][1],
                          lng: GEOJSONDATAPOLY[idx].features[jdx].geometry.coordinates[k][kk][0]
                        }
                    );
                  }
                }


                var poly = new google.maps.Polygon({
                  paths: coords,
                  strokeColor: GEOJSONDATAPOLY[idx].color,
                  strokeOpacity: 0.6,
                  strokeWeight: 2,
                  fillColor: GEOJSONDATAPOLY[idx].color,
                  fillOpacity: 0.35,
                  title: GEOJSONDATAPOLY[idx].features[jdx].properties.Name + "(" + data2.results[0].c + ")"
                });

                poly.c = data2.results[0].c;

                polygons.push( poly );


                howmanyPoly++;

                if(howmanyPoly>=(totalPoly)){
                  postProcessPoly();
                }

             };

           }

          for(var j =0; j<data.features.length; j++){

            var nome = data.features[j].properties.Name;
            console.log("Name:" + nome);
            var search = data.features[j].properties.search;
            var keywords = search.split(",");

            
            $.getJSON("../api/getMultipleKeywordStatistics", { "researches" : reserchesstring , "keywords": keywords.join(",") })
            .done(createCallback(i,j,data.features.length))
            .fail(function( jqxhr, textStatus, error ){
                //fare qualcosa in caso di fallimento
            });  

          }
          

          

        }
      });
}


function postProcessPoly(){

  for(var i = 0; i<polygons.length; i++){
    polygons[i].fillOpacity = 1*polygons[i].c/maxSizePoly;
    polygons[i].setMap( map );
  }

  console.log(polygons);

}



var maxSize = 0;
var howmany = 0;

function getLayer(i){
  d3.json("../webroot/visualizations/geography-points/GeoJSON/" + JSONLayers[i],function(error,data){
        if(!error){

          GEOJSONDATA[i]= data ;

          total = total + data.features.length;

          function createCallback(idx,jdx,tot) {
             return function(data2) {
                // use idx
                
                data2.results[0].c = +data2.results[0].c;
                data2.results[0].comfort = +data2.results[0].comfort;
                data2.results[0].energy = +data2.results[0].energy;

                exportdata.push(
                  {
                    type: "Location",
                    name: GEOJSONDATA[idx].features[jdx].properties.Name,
                    destinations: data2.results[0].c,
                    comfort: data2.results[0].comfort,
                    energy: data2.results[0].energy
                  }
                );

                var siz = 2 + data2.results[0].c;

                if(maxSize<siz){ maxSize = siz; }

                var marker = new google.maps.Marker({
                  position: {lat: parseFloat(GEOJSONDATA[idx].features[jdx].geometry.coordinates[1]), lng: parseFloat(GEOJSONDATA[idx].features[jdx].geometry.coordinates[0])},
                  map: null,
                  icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: siz,
                    fillColor: GEOJSONDATA[idx].color,
                    fillOpacity: 0.8,
                    strokeColor: 'rgba(255,255,255,0.3)',
                    strokeWeight: 1
                  },
                  title: GEOJSONDATA[idx].features[jdx].properties.Name
                });

                markers.push( marker );


                howmany++;

                if(howmany>=(total)){
                  postProcess();
                }

             };

           }

          for(var j =0; j<data.features.length; j++){

            var nome = data.features[j].properties.Name;
            var search = data.features[j].properties.search;
            var keywords = search.split(",");

            
            $.getJSON("../api/getMultipleKeywordStatistics", { "researches" : reserchesstring , "keywords": keywords.join(",") })
            .done(createCallback(i,j,data.features.length))
            .fail(function( jqxhr, textStatus, error ){
                //fare qualcosa in caso di fallimento
            });  

          }
          

          

        }
      });
}



function postProcess(){

  for(var i = 0; i<markers.length; i++){
    markers[i].icon.scale = 1 + 20*markers[i].icon.scale/maxSize;
    markers[i].setMap( map );
  }

  console.log(markers);

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