var researches;
var reserchesstring;


$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    getTimeline();

});


function exportdata(){

    window.open("../api/getEmotionsTimeline?researches=" + reserchesstring );

}

function getTimeline(){

    var margin = {top: 30, right: 20, bottom: 30, left: 50},
    width = $("#results").width() - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

    var datelength = 100;

    var parseDate = d3.time.format("%d-%m-%Y").parse;

    var x = d3.time.scale().range([0, width]);
    var y = d3.scale.linear().range([height, 0]);
    var z = d3.scale.category20c();



    var valueline = d3.svg.line()
    .x(function(d,i) { /* console.log(d); console.log(i); console.log("----"); */ return x(d.date); })
    .y(function(d,i) { return y(d.close); });


    $.getJSON("../api/getMultipleKeywordsTimeline", 
      { 
        "researches" : reserchesstring,
        "keywords" : "elettronica,rock,house,contemporanea,blues,italiana,classica"
      }
    )
    .done(function(data){

      var extents = [];
      var valextents = [];

      //  x.domain(d3.extent(dataset, function(d) { return d.date; }));
      //  y.domain([0, d3.max(dataset, function(d) { return d.close; })]);

      

      for(emotionname in data.results){

          var dataset = data.results[emotionname];
          var o = new Object();
          o.key = emotionname;
          o.values = new Array();

          dataset.forEach(function(d) {

              d.date = parseDate(d.date);
              d.close = +d.close;

              extents.push( d.date );
              valextents.push( d.close );

          });


      }



      
      var mindate = d3.min(extents); //new Date();
      var maxdate = d3.max(extents); //new Date();

      var minval = d3.min(valextents); //0;
      var maxval = d3.max(valextents); //0;

      /*
      if(extents.length>0){
        mindate = extents[0][0];
        maxdate = extents[0][1];
        for(var i = 1; i<extents.length; i++){
          if(mindate>extents[i][0]){
            mindate = extents[i][0];
          }
          if(mindate>extents[i][1]){
            mindate = extents[i][1];
          }
          if(maxdate<extents[i][0]){
            maxdate = extents[i][0];
          }
          if(maxdate<extents[i][1]){
            maxdate = extents[i][1];
          }
        }
      }*/

      var dateextent = [mindate,maxdate];


      /*
      if(valextents.length>0){
        minval = valextents[0][0];
        maxval = valextents[0][1];
        for(var i = 1; i<valextents.length; i++){
          if(minval>valextents[i][0]){
            minval = valextents[i][0];
          }
          if(minval>valextents[i][1]){
            minval = valextents[i][1];
          }
          if(maxval<valextents[i][0]){
            maxval = valextents[i][0];
          }
          if(maxval<valextents[i][1]){
            maxval = valextents[i][1];
          }
        }
      }
      */

      var valextent = [minval,maxval];

      x.domain( dateextent );
      y.domain( valextent );


      var xAxis = d3.svg.axis().scale(x)
      .orient("bottom").ticks(10)
      .tickFormat(d3.time.format("%Y-%m-%d"));;

      var yAxis = d3.svg.axis().scale(y)
      .orient("left").ticks(5);

      
        for(emotionname in data.results){

          var dataset = data.results[emotionname];

          //console.log(dataset);

          var datevalues = new Array();

          dataset.forEach(function(d) {

              //d.date = parseDate(d.date);
              //d.close = +d.close;

              var y = "" + d.date.getFullYear();
              var m = "" + (d.date.getMonth() + 1);
              if(d.date.getMonth()<10){ m = "0" + m; }
              var da = "" + (d.date.getDay() + 1);
              if(d.date.getDay()<10){ da = "0" + da; }

              datevalues[y + m + da] = d.close;

          });


                var div = d3.select("#results")
                  .append("div")
                    .attr("class" , "emtionline");

                  div.append("h2")
                    .text(emotionname);

                var svg = div.append("svg")
                      .attr("width", width + margin.left + margin.right)
                      .attr("height", height + margin.top + margin.bottom + datelength )
                  .append("g")
                      .attr("transform", 
                        "translate(" + margin.left + "," + margin.top + ")");

              // Scale the range of the data
              //console.log(  d3.extent(dataset, function(d) { return d.date; })  );
              //  x.domain(d3.extent(dataset, function(d) { return d.date; }));
              //  y.domain([0, d3.max(dataset, function(d) { return d.close; })]);

                // Add the valueline path.
                svg.append("path")
                    .attr("class", "line")
                    .attr("d", valueline(dataset));

                dataset.forEach(function(d){
                    //console.log(d);
                    svg.append("circle")
                    .attr('r',function(d){  return 5;  })
                    .attr("class", "point")
                    .attr('cx', x(d.date) )
                    .attr('cy', y(d.close) )
                    .fill('black');
                });

                // Add the X Axis
                svg.append("g")
                    .attr("class", "x axis")
                    .attr("transform", "translate(0," + height + ")")
                    .call(xAxis)
                    .selectAll("text")
                      .attr("y", 0)
                      .attr("x", 9)
                      .attr("dy", ".35em")
                      .attr("transform", "rotate(90)")
                      .style("text-anchor", "start");

                // Add the Y Axis
                svg.append("g")
                    .attr("class", "y axis")
                    .call(yAxis);

        }


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