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


    getCEDistribution(true);

});


function exportdata(){

    window.open("../api/getEnergyComfortDistribution?researches=" + reserchesstring );

}


function getCEDistribution(clearGraph){

    var margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = $("#results").width() - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;


    var xValue = function(d) { return d.energy;}, // data -> value
    xScale = d3.scale.linear().range([0, width]), // value -> display
    xMap = function(d) { return xScale(xValue(d));}, // data -> display
    xAxis = d3.svg.axis().scale(xScale).orient("bottom");

    var yValue = function(d) { return d.comfort;}, // data -> value
    yScale = d3.scale.linear().range([height, 0]), // value -> display
    yMap = function(d) { return yScale(yValue(d));}, // data -> display
    yAxis = d3.svg.axis().scale(yScale).orient("left");

    var cValue = function(d) { return d.c;},
    color = d3.scale.category10();


    var svg = d3.select("#results").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var tooltip = d3.select("body").append("div")
    .attr("class", "tooltip")
    .style("opacity", 0);




    $.getJSON("../api/getEnergyComfortDistribution", { "researches" : reserchesstring , "limit" : limit , "language": language})
    .done(function(data){

        data = data.results;

        data.forEach(function(d) {
            d.comfort = +d.comfort;
            d.energy = +d.energy;
        });



        var minc = d3.min(data,function(d){ return d.c; });
        var maxc = d3.max(data,function(d){ return d.c; });

        var meancomfort = d3.mean(data,function(d){ return d.comfort; })
        var meanenergy = d3.mean(data,function(d){ return d.energy; })


        var rscale = d3.scale.linear()
                    .domain([minc, maxc])
                    .range([3, 20]);

        // don't allow points to overlap to axis
        xScale.domain([d3.min(data, xValue)-1, d3.max(data, xValue)+1]);
        yScale.domain([d3.min(data, yValue)-1, d3.max(data, yValue)+1]);

        // x-axis
        svg.append("g")
              .attr("class", "x axis")
              .attr("transform", "translate(0," + height + ")")
              .call(xAxis)
            .append("text")
              .attr("class", "label")
              .attr("x", width)
              .attr("y", -6)
              .style("text-anchor", "end")
              .text("Energy");

        // y-axis
        svg.append("g")
              .attr("class", "y axis")
              .call(yAxis)
            .append("text")
              .attr("class", "label")
              .attr("transform", "rotate(-90)")
              .attr("y", 6)
              .attr("dy", ".71em")
              .style("text-anchor", "end")
              .text("Comfort");

        svg.append("line")
            .attr("class","crosshair")
            .attr("x1", 0)
            .attr("y1", yScale(0))
            .attr("x2", width)
            .attr("y2", yScale(0));

        svg.append("line")
            .attr("class","crosshair")
            .attr("x1", xScale(0))
            .attr("y1", 0)
            .attr("x2", xScale(0))
            .attr("y2", height);

        svg.append("circle")
            .attr("class" , "mean")
            .attr("r" , 25)
            .attr("cx" , xScale(meanenergy))
            .attr("cy" , yScale(meancomfort));

        // draw dots
        svg.selectAll(".dot")
              .data(data)
            .enter().append("circle")
              .attr("class", "dot")
              .attr("r", function(d){ return rscale(d.c); })
              .attr("cx", xMap)
              .attr("cy", yMap) 
              .on("mouseover", function(d) {
                  tooltip.transition()
                       .duration(200)
                       .style("opacity", .9);
                  tooltip.html("(" + xValue(d) 
                    + ", " + yValue(d) + ")")
                       .style("left", (d3.event.pageX + 5) + "px")
                       .style("top", (d3.event.pageY - 28) + "px");
              })
              .on("mouseout", function(d) {
                  tooltip.transition()
                       .duration(500)
                       .style("opacity", 0);
              });

        
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




function myGraph(el) {

    // Add and remove elements on the graph object
    this.addNode = function (obj) {
        nodes.push(obj);
        update();
    }

    this.removeAllNodes = function(){
        var i = 0;
        while(i<nodes.length && nodes.length>0){



            var j = 0;
            var n = nodes[i];
            while (j < links.length) {
                if ((links[j]['source'] === n)||(links[j]['target'] == n)) links.splice(j,1);
                else j++;
            }
            var index = findNodeIndex(n.id);
            if(index !== undefined) {
                nodes.splice(index, 1);
                update();
            }




        }
    }

    this.removeNode = function (id) {
        var i = 0;
        var n = this.findNode(id);
        while (i < links.length) {
            if ((links[i]['source'] === n)||(links[i]['target'] == n)) links.splice(i,1);
            else i++;
        }
        var index = findNodeIndex(id);
        if(index !== undefined) {
            nodes.splice(index, 1);
            update();
        }
    }

    this.addLink = function (sourceId, targetId,c) {
        var sourceNode = this.findNode(sourceId);
        var targetNode = this.findNode(targetId);

        //console.log("--addLink--[" + sourceId + "," + targetId + "]");
        //console.log(sourceNode);
        //console.log(targetNode);

        if((sourceNode !== undefined) && (targetNode !== undefined)) {
            //console.log("[aggiungo]");
            links.push({"source": sourceNode, "target": targetNode, "c": c});
            update();
        }
    }

    this.findNode = function (id) {
        for (var i=0; i < nodes.length; i++) {
            if (nodes[i].id === id)
                return nodes[i]
        };
    }

    var findNodeIndex = function (id) {
        for (var i=0; i < nodes.length; i++) {
            if (nodes[i].id === id)
                return i
        };
    }

    this.getNodes = function(){
        return nodes;
    }

    this.getlinks = function(){
        return links;
    }

    // set up the D3 visualisation in the specified element
    var w = $(el).innerWidth(),
        h = 500; //$(el).innerHeight();

    var vis = this.vis = d3.select(el).append("svg:svg")
        .attr("width", w)
        .attr("height", h)
        //.attr("pointer-events", "all")
        .append("g")
        .call(d3.behavior.zoom().on("zoom", redraw))
        .append("g");

    vis.append('svg:rect')
    .attr('width', 20000)
    .attr('height', 20000)
    .attr('x' , -10000)
    .attr('y', -10000)
    .attr('fill', 'white');

    var force = d3.layout.force()
        .gravity(.05)
        .distance(100)
        .charge(-200)
        .size([w, h]);

    var nodes = force.nodes(),
        links = force.links();

    

    var update = function () {

        var link = vis.selectAll("line.linkexplore")
            .data(links, function(d) { return d.source.id + "-" + d.target.id; });

        link.enter().insert("line")
            .attr("class", "linkexplore")
            .attr("stroke-width", function(d){
                //console.log(d);
                return Math.min(10,d.c);
            });

        link.exit().remove();

        var node = vis.selectAll("g.node")
            .data(nodes, function(d) { return d.id;});

        var nodeEnter = node.enter().append("g")
            .attr("class", "node")
            .on("click",clickedNode)
            .call(force.drag);

        nodeEnter.append("text")
            .attr("class", "nodetextexplore")
            .attr("dx", 12)
            .attr("dy", ".35em")
            .text(function(d) {return d.id});

        nodeEnter.append("circle")
            .attr("class", "circleexplore")
            .attr("cx", "0px")
            .attr("cy", "0px")
            .attr("r" , "5px")
            .attr("width", "16px")
            .attr("height", "16px");

        node.exit().remove();

        force.on("tick", function() {
          link.attr("x1", function(d) { return d.source.x; })
              .attr("y1", function(d) { return d.source.y; })
              .attr("x2", function(d) { return d.target.x; })
              .attr("y2", function(d) { return d.target.y; });

          node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
        });

        // Restart the force layout.
        force.start();
    }

    // Make it all go
    update();
}


var clickedNode = function(d){
    if(d3.event.shiftKey){
        //window.open( d.pu  );
    } else if(d3.event.altKey){
        //$("input#searchbox").val( d.id  );
        //doSearch(true);    
    }else {
        //$("input#searchbox").val( d.id  );
        //doSearch(false);    
    }
    
};


function redraw (){
      //console.log("zoom", d3.event.translate, d3.event.scale);
        graph.vis.attr("transform", 
                 "translate(" + d3.event.translate + ")" 
                    + " scale(" + d3.event.scale + ")"
                 );
    }