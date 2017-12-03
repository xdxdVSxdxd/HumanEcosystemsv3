var groups = [
                {
                  name: "Traffico",
                  slug: "Traffico",
                  search: "%raffic%",
                  hue: 0.1
                },
                {
                  name: "TDays",
                  slug: "TDays",
                  search: "%t-days%,%tdays%,%TDAYS%,%T-DAYS%",
                  hue: 0.2
                },
                {
                  name: "Traffico Limitato",
                  slug: "TrafficoLimitato",
                  search: "%limitato%",
                  hue: 0.3
                },
                {
                  name: "Wek End",
                  slug: "weekend",
                  search: "%weekend%,%week end%,%week-end%,%fine settimana%",
                  hue: 0.3
                },
                {
                  name: "Centro Storico",
                  slug: "CentroStorico",
                  search: "%centro%,%centro storico%",
                  hue: 0.3
                },
                {
                  name: "Pedoni",
                  slug: "pedoni",
                  search: "%pedon%",
                  hue: 0.3
                }

              ];

var subjects = [
                    {
                        name: "TDAYS",
                        search: "tdays",
                        mention: "#tdays"
                    },
                    {
                        name: "TRAFFICO",
                        search: "traffico",
                        mention: "#traffico"
                    },
                    {
                        name: "MOBILITÀ",
                        search: "mobilità",
                        mention: "#mobilità"
                    },
                    {
                        name: "CENTRO",
                        search: "centro",
                        mention: "#centro"
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