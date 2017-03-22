var groups = [
                {
                  name: "Rock",
                  slug: "Rock",
                  search: "%rock%",
                  hue: 0.1
                },
                {
                  name: "Blues",
                  slug: "Blues",
                  search: "%blues%",
                  hue: 0.2
                },
                {
                  name: "Reggae",
                  slug: "Reggae",
                  search: "%regga%,% dub%",
                  hue: 0.3
                },
                {
                  name: "Jazz",
                  slug: "Jazz",
                  search: "%jazz%",
                  hue: 0.4
                },
                {
                  name: "Punk",
                  slug: "Punk",
                  search: "%punk%",
                  hue: 0.5
                },
                {
                  name: "Pop",
                  slug: "Pop",
                  search: "%pop %",
                  hue: 0.6
                },
                {
                  name: "Italiana",
                  slug: "Italiana",
                  search: "%italian%",
                  hue: 0.7
                },
                {
                  name: "Classical",
                  slug: "Classical",
                  search: "%classica%",
                  hue: 0.8
                },
                {
                  name: "Hip Hop",
                  slug: "Hip_Hop",
                  search: "%hip hop%,%hiphop%,% rap",
                  hue: 0.9
                },
                {
                  name: "Electronica",
                  slug: "Electronica",
                  search: "%electronic%,%techn%,%house%",
                  hue: 1.0
                }

              ];

var subjects = [
                    {
                        name: "BASE",
                        search: "base,basemilano",
                        mention: "@basemilano"
                    },
                    {
                        name: "Alcatraz",
                        search: "alcatraz",
                        mention: "@AlcatrazMilano"
                    },
                    {
                        name: "Circolo Magnolia",
                        search: "magnolia",
                        mention: "@CircoloMagnolia"
                    },
                    {
                        name: "Cascina Martesana",
                        search: "martesana",
                        mention: "@cMartesana"
                    },
                    {
                        name: "Serraglio",
                        search: "serraglio",
                        mention: "#serraglio"
                    },
                    {
                        name: "Leoncavallo",
                        search: "leoncavallo",
                        mention: "@leoncavallospa"
                    },
                    {
                        name: "mare culturale urbano",
                        search: "maremilano,culturaleurbano",
                        mention: "@maremilano"
                    },
                    {
                        name: "Fabrique",
                        search: "fabrique",
                        mention: "@FabriqueMilano"
                    },
                    {
                        name: "Santeria Social Club",
                        search: "santeria",
                        mention: "@SanteriaMilano"
                    },
                    {
                        name: "La Fabbrica del Vapore",
                        search: "fabbricavapore,fabbrica del vapore,fdvlab",
                        mention: "@fdvlab"
                    },
                    {
                        name: "DIVINA",
                        search: "divina",
                        mention: "@priveladivina"
                    },
                    {
                        name: "Atomic Bar",
                        search: "atomicbar,atomic bar",
                        mention: "@ATOMICBARMILANO"
                    },
                    {
                        name: "Plastic Club",
                        search: "Plastic Club",
                        mention: "#plasticclub"
                    },
                    {
                        name: "Hollywood",
                        search: "HollywoodRythmoteque,HollywoodMilano,hollywood",
                        mention: "@HollywoodMilano"
                    },
                    {
                        name: "Tocqueville 13",
                        search: "Tocqueville",
                        mention: "#tocqueville"
                    },
                    {
                        name: "Old Fashion Club",
                        search: "oldfashionclub,Old Fashion",
                        mention: "@oldfashionclub"
                    },
                    {
                        name: "ZOG",
                        search: "ZOG",
                        mention: "#ZOG"
                    },
                    {
                        name: "Byblos Milano Club",
                        search: "Byblos Milano Club,byblosmilano",
                        mention: "@byblosmilano"
                    },
                    {
                        name: "Armani Prive",
                        search: "ArmaniPrive,armani",
                        mention: "@armani"
                    },
                    {
                        name: "Hollywood",
                        search: "HollywoodRythmoteque,HollywoodMilano,hollywood",
                        mention: "@HollywoodMilano"
                    },
                    {
                        name: "Dude Club",
                        search: "Dude Club,DudeClubMilano",
                        mention: "@DudeClubMilano"
                    },
                    {
                        name: "Lo-Fi",
                        search: "Lo-Fi,lofimilano",
                        mention: "@lofimilano"
                    },
                    {
                        name: "Upcycle Milano Bike Cafè",
                        search: "Upcycle,upcycle",
                        mention: "#upcycle"
                    },
                    {
                        name: "Gattò Robe e Cucina",
                        search: "Gattò,robe_e_cucina",
                        mention: "@robe_e_cucina"
                    },
                    {
                        name: "Standards Studio",
                        search: "Standards Studio,standardstudio",
                        mention: "#standardstudio"
                    },
                    {
                        name: "Blue Note Milano",
                        search: "Blue Note,bluenote",
                        mention: "@Bluenotemilano"
                    },
                    {
                        name: "Discoteca Tunnell",
                        search: "Discoteca Tunnel,TunnelClubMi",
                        mention: "@TunnelClubMi"
                    },
                    {
                        name: "Biko Club",
                        search: "Biko Club,biko",
                        mention: "#bikoclub"
                    },
                    {
                        name: "RockNRoll Club",
                        search: "Rock N Roll,rocknrollclub",
                        mention: "#rocknrollclub"
                    },
                    {
                        name: "La Buca Di San Vincenzo",
                        search: "San Vincenzo,labucadisanvincenzo",
                        mention: "#labucadisanvincenzo"
                    },
                    {
                        name: "Masada",
                        search: "Masada,CircoloMasada",
                        mention: "@CircoloMasada"
                    },
                    {
                        name: "Arci l'Impegno",
                        search: "arci impegno,Limpegno",
                        mention: "@Limpegno"
                    },
                    {
                        name: "Cascina Autogestita Torchiera senzacqua",
                        search: "torchiera",
                        mention: "#torchiera"
                    },
                    {
                        name: "Macao",
                        search: "Macao,MacaoTwit",
                        mention: "@MacaoTwit"
                    },
                    {
                        name: "Magazzini Generali",
                        search: "MagazziniGenerali,MagGenerali",
                        mention: "@MagGenerali"
                    },
                    {
                        name: "Cox 18",
                        search: "Cox 18,Cox18,CSOA_COX18",
                        mention: "@CSOA_COX18"
                    },
                    {
                        name: "Spirit de Milan",
                        search: "Spirit de Milan",
                        mention: "#SpiritdeMilan"
                    },
                    {
                        name: "La Balera dell'Ortica",
                        search: "Balera Ortica,baleradellortica",
                        mention: "#baleradellortica"
                    },
                    {
                        name: "La Scighera",
                        search: "Scighera",
                        mention: "@DudeClubMilano"
                    },
                    {
                        name: "Circolo Arci Bellezza",
                        search: "Arci Belleza,ArciBellezza",
                        mention: "#ArciBellezza"
                    }
                ];



var researches;
var reserchesstring;
var mode = "MONTH";

$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    getResults();

});


function exportdata(){

    //window.open("../api/getEmotions?researches=" + reserchesstring );

}

function getResults(){

    var container = d3.select("#results").append("div")
        .attr("class","overcontainer")
        .style("width",  (subjects.length*220) + "px");




    subjects.forEach(function(d,i){

        container.append("div")
            .attr("class", "opContainer")
            .attr("id","op-" + i);

        function handleResults(dd,idx){
            return function(data){
                // handle

                //console.log(idx);
                //console.log(dd);
                //console.log(data);

                var co = d3.select("#op-" + idx);

                co.append("div")
                    .attr("class","op-title")
                    .text(dd.name);

                




                

                var con1 = co.append("div")
                    .attr("class","op-radar")
                    .attr("id","op-emotions-" + idx);

                var ems = getKeys( data.results.emotions );
                var values = [];

                for(var ii = 0; ii<ems.length; ii++){
                    values.push(
                        [ +data.results.emotions[ems[ii]].comfort , +data.results.emotions[ems[ii]].energy  ]
                    );
                }

                Highcharts.chart("op-emotions-" + idx, {

                    chart: {
                        type: 'scatter',
                        zoomType: 'xy',
                        width: 200,
                        height: 200
                    },

                    credits: {
                        enabled: false
                    },

                    title: {
                        text: null
                    },

                    xAxis: {
                        title: {
                            enabled: true,
                            text: 'Comfort'
                        },
                        startOnTick: true,
                        endOnTick: true,
                        showLastLabel: true
                    },

                    yAxis: {
                        title: {
                            text: 'Energy'
                        }
                    },

                    tooltip: {
                        shared: true,
                        pointFormat: '<span style="color:{series.color}">{series.name}: <b>${point.y:,.0f}</b><br/>'
                    },

                    legend: { 
                        enabled: false
                    },

                    series: [{
                        name: 'emotions',
                        color: 'rgba(119, 152, 191, .5)',
                        data: values
                    }],

                    plotOptions: {
                        scatter: {
                            marker: {
                                radius: 3,
                                states: {
                                    hover: {
                                        enabled: true,
                                        lineColor: 'rgb(100,100,100)'
                                    }
                                }
                            },
                            states: {
                                hover: {
                                    marker: {
                                        enabled: false
                                    }
                                }
                            },
                            tooltip: {
                                headerFormat: '<b>{series.name}</b><br>',
                                pointFormat: '{point.x} comfort, {point.y} energy'
                            }
                        }
                    }

                });




                var con1 = co.append("div")
                    .attr("class","op-radar")
                    .attr("id","op-groups-" + idx);


                ems = [];
                for(var i = 0; i<data.results.groups.length; i++){
                    ems.push( data.results.groups[i].name );
                }
                values = [];
                for(var i = 0; i<data.results.groups.length; i++){
                    values.push( data.results.groups[i].n );
                }

                for(var ii = 0; ii<ems.length; ii++){
                    values.push(
                        data.results.emotions[ems[ii]]
                    );
                }


                Highcharts.chart("op-groups-" + idx, {

                    chart: {
                        polar: true,
                        type: 'line',
                        width: 200,
                        height: 200
                    },

                    credits: {
                        enabled: false
                    },

                    title: {
                        text: null
                    },

                    pane: {
                        size: '80%'
                    },

                    xAxis: {
                        categories: ems,
                        tickmarkPlacement: 'on',
                        lineWidth: 0,
                        title: {
                            text: null
                        },
                        labels: {
                            enabled: false
                        }

                    },

                    yAxis: {
                        gridLineInterpolation: 'polygon',
                        lineWidth: 0,
                        min: 0
                    },

                    tooltip: {
                        shared: true,
                        pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><br/>'
                    },

                    legend: { 
                        enabled: false
                    },

                    series: [{
                        name: 'Genres',
                        data: values,
                        pointPlacement: 'on'
                    }]

                });





                var con2 = co.append("div")
                    .attr("class","op-radar")
                    .attr("id","op-days-" + idx);


                ems = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
                values = [];
                for(var i = 0; i<ems.length; i++){
                    values.push( data.results.daysofweek[ems[i]] );
                }


                Highcharts.chart("op-days-" + idx, {

                    chart: {
                        polar: true,
                        type: 'line',
                        width: 200,
                        height: 200
                    },

                    credits: {
                        enabled: false
                    },

                    title: {
                        text: null
                    },

                    pane: {
                        size: '80%'
                    },

                    xAxis: {
                        categories: ems,
                        tickmarkPlacement: 'on',
                        lineWidth: 0,
                        title: {
                            text: null
                        },
                        labels: {
                            enabled: false
                        }

                    },

                    yAxis: {
                        gridLineInterpolation: 'polygon',
                        lineWidth: 0,
                        min: 0
                    },

                    tooltip: {
                        shared: true,
                        pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><br/>'
                    },

                    legend: { 
                        enabled: false
                    },

                    series: [{
                        name: 'Days',
                        data: values,
                        pointPlacement: 'on'
                    }]

                });








                var con3 = co.append("div")
                    .attr("class","op-radar")
                    .attr("id","op-stats-" + idx);


                ems = [];
                values = [];

                Highcharts.chart("op-stats-" + idx, {

                    chart: {
                        type: 'column',
                        width: 200,
                        height: 200
                    },

                    credits: {
                        enabled: false
                    },

                    title: {
                        text: null
                    },

                    pane: {
                        size: '80%'
                    },

                    xAxis: {
                        categories:  [
                            'Engagement',
                            'Mentions'
                        ],
                        crosshair: true

                    },

                    yAxis: {
                        min: 0,
                        title: {
                            text: null
                        }
                    },

                    tooltip: {
                        shared: true,
                        pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><br/>'
                    },

                    legend: { 
                        enabled: false
                    },

                    series: [{
                        name: 'Statistics',
                        data: [  data.results.stats.engagements,  data.results.stats.mentions ],
                        pointPlacement: 'on'
                    }],
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    }

                });


                var con4 = co.append("div")
                    .attr("class","op-radar")
                    .attr("id","op-ratio-" + idx);

                con4.append("div")
                    .attr("class","op-title-small")
                    .text("RATIO");

                con4.append("div")
                    .attr("class","op-ratio")
                    .text( "" + ( data.results.stats.engagements / data.results.stats.mentions ).toFixed(2) );

            }
        };



        $.getJSON("../api/getMultipleSubjects", { "researches" : reserchesstring, "mode" : mode, "search" : d.search, "mention": d.mention , "groups" : groups })
        .done(handleResults(d,i))
        .fail(function( jqxhr, textStatus, error ){
            //fare qualcosa in caso di fallimento
        });

    });

    

}

var getKeys = function(obj){
   var keys = [];
   for(var key in obj){
      keys.push(key);
   }
   return keys;
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