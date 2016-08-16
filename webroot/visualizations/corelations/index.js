var researches;
var reserchesstring;

var graph;


var limit = 50;

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

    getRelations();

});



var margin = {top: 80, right: 10, bottom: 10, left: 80},
    width = 2000,
    height = 2000;

var x,z,c;

var oos = ["name","count","group"];

var svg;

function exportdata(){

    window.open("../api/getWordNetwork?researches=" + reserchesstring );

}


function getRelations(){

    $("#results").width( width + margin.left + margin.right );
    $("#results").height( height + margin.top + margin.bottom );

    x = d3.scale.ordinal().rangeBands([0, width]);
    z = d3.scale.linear().domain([0, 4]).clamp(true);
    c = d3.scale.category10().domain(d3.range(10));

    svg = d3.select("#results").append("svg")
        .attr("width", width + margin.left)// - margin.left - margin.right)
        .attr("height", height + margin.top)// - margin.top - margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    $.getJSON("../api/getWordNetwork", { "researches" : reserchesstring , "limit" : limit , "language": language})
    .done(function(grapho){

                var matrix = [],
                nodes = grapho.nodes,
                n = nodes.length;

                nodes.forEach(function(node, i) {
                    //"id":"103939","name":"MILANO","n":"484","group":"484","idx":0
                    //node.id = node.i;
                    node.name = node.word;
                    node.n = +node.weight;
                    //node.group = +node.weight;
                    node.energy = +node.energy;
                    node.comfort = +node.comfort;
                    if(node.energy>0 && node.comfort>0){
                        node.group = 1;
                    } else if(node.energy<0 && node.comfort>0){
                        node.group = 2;
                    } else if(node.energy>0 && node.comfort<0){
                        node.group = 3;
                    } else if(node.energy<0 && node.comfort<0){
                        node.group = 4;
                    } else {
                        node.group = 0;
                    }
                    node.idx = i;
                    node.index = i;
                    node.count = 0;
                    matrix[i] = d3.range(n).map(function(j) { return {x: j, y: i, z: 0}; });
                });

                
                grapho.links.forEach(function(link) {
                    link.value = link.weight;
                    matrix[link.sourceid][link.targetid].z += link.value;
                    matrix[link.targetid][link.sourceid].z += link.value;
                    matrix[link.sourceid][link.sourceid].z += link.value;
                    matrix[link.targetid][link.targetid].z += link.value;

                    nodes[link.sourceid].count += link.value;
                    nodes[link.targetid].count += link.value;

                  });

                var orders = {
                    name: d3.range(n).sort(function(a, b) { return d3.ascending(nodes[a].name, nodes[b].name); }),
                    count: d3.range(n).sort(function(a, b) { return nodes[b].count - nodes[a].count; }),
                    group: d3.range(n).sort(function(a, b) { return nodes[b].group - nodes[a].group; })
                };

                x.domain(orders.name);

                svg.append("rect")
                    .attr("class", "background")
                    .attr("width", width)
                    .attr("height", height);

                var row = svg.selectAll(".row")
                    .data(matrix)
                    .enter().append("g")
                    .attr("class", "row")
                    .attr("transform", function(d, i) { return "translate(0," + x(i) + ")"; })
                    .each(row);

                row.append("line")
                    .attr("x2", width);

                row.append("text")
                    .attr("x", -6)
                    .attr("y", x.rangeBand() / 2)
                    .attr("dy", ".32em")
                    .attr("text-anchor", "end")
                    .text(function(d, i) { return nodes[i].name; });

                var column = svg.selectAll(".column")
                    .data(matrix)
                    .enter().append("g")
                    .attr("class", "column")
                    .attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });

                column.append("line")
                    .attr("x1", -width);

                column.append("text")
                    .attr("x", 6)
                    .attr("y", x.rangeBand() / 2)
                    .attr("dy", ".32em")
                    .attr("text-anchor", "start")
                    .text(function(d, i) { return nodes[i].name; });


                function row(row) {
                    //console.log(row);
                    var cell = d3.select(this).selectAll(".cell")
                        .data(row.filter(function(d) {
                            /*
                            console.log("[filter]");
                            console.log(d);
                            console.log("--------");
                            */
                            return d.z; 
                        }))
                    .enter().append("rect")
                        .attr("class", "cell")
                        .attr("x", function(d) { return x(d.x); })
                        .attr("width", x.rangeBand())
                        .attr("height", x.rangeBand())
                        .style("fill-opacity", function(d) { return z(d.z); })
                        .style("fill", function(d) { 

                            var energytot = nodes[d.x].energy + nodes[d.y].energy;
                            var comforttot = nodes[d.x].comfort + nodes[d.y].comfort;
                            var group = 0;

                            if(energytot>0 && comforttot>0){
                                group = 1;
                            } else if(energytot<0 && comforttot>0){
                                group = 2;
                            } else if(energytot>0 && comforttot<0){
                                group = 3;
                            } else if(energytot<0 && comforttot<0){
                                group = 4;
                            } else {
                                group = 0;
                            }

                            return c(group); 
                        })
                        .on("mouseover", mouseover)
                        .on("mouseout", mouseout);
                }

                function mouseover(p) {
                    d3.selectAll(".row text").classed("active", function(d, i) { return i == p.y; });
                    d3.selectAll(".column text").classed("active", function(d, i) { return i == p.x; });
                }


                function mouseout() {
                    d3.selectAll("text").classed("active", false);
                }

                d3.select("#order").on("change", function() {
                    order(this.value);
                });

                function order(value) {
                    x.domain(orders[value]);

                    var t = svg.transition().duration(2500);

                    t.selectAll(".row")
                        .delay(function(d, i) { return x(i) * 4; })
                        .attr("transform", function(d, i) { return "translate(0," + x(i) + ")"; })
                        .selectAll(".cell")
                            .delay(function(d) { return x(d.x) * 4; })
                            .attr("x", function(d) { return x(d.x); });

                    t.selectAll(".column")
                        .delay(function(d, i) { return x(i) * 4; })
                        .attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });
                }
        
    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });

}


var parseDate = d3.time.format("%Y %m %d %H:%M").parse;





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