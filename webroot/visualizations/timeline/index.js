var researches;
var reserchesstring;


$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    getTimeline();

});


function exportdata(){

    window.open("../api/getTimeline?researches=" + reserchesstring );

}

function getTimeline(){

    var margin = {top: 30, right: 20, bottom: 30, left: 50},
    width = $("#results").width() - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

    var parseDate = d3.time.format("%d-%m-%Y").parse;

    var x = d3.time.scale().range([0, width]);
    var y = d3.scale.linear().range([height, 0]);

    var xAxis = d3.svg.axis().scale(x)
    .orient("bottom").ticks(5);

    var yAxis = d3.svg.axis().scale(y)
    .orient("left").ticks(5);

    var valueline = d3.svg.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.close); });

    var svg = d3.select("#results")
        .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
        .append("g")
            .attr("transform", 
              "translate(" + margin.left + "," + margin.top + ")");

    $.getJSON("../api/getTimeline", { "researches" : reserchesstring })
    .done(function(data){

        //console.log(data.results);

        var datevalues = new Array();

        data.results.forEach(function(d) {

            d.date = parseDate(d.date);
            d.close = +d.close;

            var y = "" + d.date.getFullYear();
            var m = "" + (d.date.getMonth() + 1);
            if(d.date.getMonth()<10){ m = "0" + m; }
            var da = "" + (d.date.getDay() + 1);
            if(d.date.getDay()<10){ da = "0" + da; }

            datevalues[y + m + da] = d.close;

        });

            // Scale the range of the data
              x.domain(d3.extent(data.results, function(d) { return d.date; }));
              y.domain([0, d3.max(data.results, function(d) { return d.close; })]);

              // Add the valueline path.
              svg.append("path")
                  .attr("class", "line")
                  .attr("d", valueline(data.results));

              // Add the X Axis
              svg.append("g")
                  .attr("class", "x axis")
                  .attr("transform", "translate(0," + height + ")")
                  .call(xAxis);

              // Add the Y Axis
              svg.append("g")
                  .attr("class", "y axis")
                  .call(yAxis);


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