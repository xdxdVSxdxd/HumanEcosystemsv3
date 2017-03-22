var researches;
var reserchesstring;


var limit = 10000;

var language = "XXX";

var mode = "ALL";

var topic = "";


$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    if(getUrlParameter("limit")!=null){
        limit = getUrlParameter("limit");
    }

    if(getUrlParameter("topic")!=null){
        topic = getUrlParameter("topic");
        $("#topic").val(topic);
    }

    if(getUrlParameter("language")!=null){
        language = getUrlParameter("language");
    }


    $("#submit-topic").click(function(){
        topic = $("#topic").val();
        getTopicRelations(true);
        getTopicStatistics();
    });


    
});

function exportdata(){

    //window.open("../api/getHashtagCloud?researches=" + reserchesstring );

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



function exportTopicStats(){
    window.open('../api/getSingleHashtagStatistics?researches=' + reserchesstring + '&topic=' + encodeURIComponent(topic) + '&limit=' + limit + '&mode=' + mode);
}


function getTopicStatistics(){


  $.getJSON("../api/getSingleHashtagStatistics", { "researches" : reserchesstring ,  "topic" : topic, "mode" : mode, "limit" : limit })
    .done(function(data){

      console.log(data);

      var totnumber = 0;
      var totcomfort = 0;
      var totenergy = 0;

      data.results.forEach(function(d) {
        d.Number = +d.Number;
        d.Comfort = +d.Comfort;
        d.Energy = +d.Energy;
      });

      for(var i = 0; i<data.results.length; i++){
        totnumber = totnumber + data.results[i].Number;
        totcomfort = totcomfort + data.results[i].Comfort;
        totenergy = totenergy + data.results[i].Energy;
      }
      if(data.results.length>0){
        totcomfort = totcomfort / data.results.length;
        totenergy = totenergy / data.results.length;
      }
      

      var content = "";
      content = content + "<strong>Number:</strong> " + totnumber + "<br />";
      content = content + "<strong>Comfort:</strong> " + totcomfort + "<br />";
      content = content + "<strong>Energy:</strong> " + totenergy + "<br />";

      $("#results2stats").html( content );
      $("#results2graph").html("");

      var margin = {top: 20, right: 20, bottom: 70, left: 40},
      width = $("#results2graph").width() - margin.left - margin.right,
      height = 300 - margin.top - margin.bottom;

      var x = d3.scale.ordinal().rangeRoundBands([0, width], .05);
      var y = d3.scale.linear().range([height, 0]);

      var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

      var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left")
        .ticks(10);

      var svg = d3.select("#results2graph").append("svg")
          .attr("width", width + margin.left + margin.right)
          .attr("height", height + margin.top + margin.bottom)
        .append("g")
          .attr("transform", 
                "translate(" + margin.left + "," + margin.top + ")");
  
      x.domain(data.results.map(function(d) { return d.Language; }));
      y.domain([0, d3.max(data.results, function(d) { return d.Number; })]);

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis)
          .selectAll("text")
            .style("text-anchor", "end")
            .attr("dx", "-.8em")
            .attr("dy", "-.55em")
            .attr("transform", "rotate(-90)" );

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)
          .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Number");

        svg.selectAll("bar")
            .data(data.results)
          .enter().append("rect")
            .style("fill", function(d){
              var color = "#358BC7";
              if(d.Energy>=0 && d.Comfort>=0){
                color = "#37C735";
              } else if(d.Energy<0 && d.Comfort>=0){
                color = "#C0C735";
              } else if(d.Energy<0 && d.Comfort<0){
                color = "#C74435";
              } else {
                color = "#C735A6";
              }
              return color;
            })
            .attr("x", function(d) { return x(d.Language); })
            .attr("width", x.rangeBand())
            .attr("y", function(d) { return y(d.Number); })
            .attr("height", function(d) { return height - y(d.Number); });



    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });


}


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
    window.open('../api/getSingleHashtagNetwork?researches=' + reserchesstring + '&topic=' + encodeURIComponent(topic) + '&limit=' + limit + '&mode=' + mode);
}
function getTopicRelations(clearGraph){
        $("#results").html("");
        wgwidth = $("#results").width();
        wgheight = 900;
        $("#results").height( wgheight );

        wgcolor = d3.scale.category20();

        wgforce = d3.layout.force()
          .linkDistance(40)
          .linkStrength(0.4)
          .charge(-400)
          .size([wgwidth, wgheight]);

        wgforce.drag()
          .on("dragstart", function() { d3.event.sourceEvent.stopPropagation(); });

        wgsvg1 = d3.select("#results").append("svg")
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
        $.getJSON("../api/getSingleHashtagNetwork", { "researches" : reserchesstring ,  "topic" : topic, "mode" : mode, "limit" : limit })
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
                  topic = d.label;
                  $("#topic").val( d.label );
                  getTopicRelations(true);
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

              link
              .attr("d", function(d) {
                return "M" + d[0].x + "," + d[0].y + "S" + d[1].x + "," + d[1].y + " " + d[2].x + "," + d[2].y;
              });
              //.attr("stroke-width",function(d){
              //  return d[0].weight;
              //});;
              
              

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