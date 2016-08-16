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


    getWordCloud(true);

});

function exportdata(){

    window.open("../api/getWordCloud?researches=" + reserchesstring );

}

function getWordCloud(clearGraph){

    var tcbleed, tcwidth, tcheight;
        var tcpack;
        var tcsvg;

        tcbleed = 100;
        tcwidth = $("#results").width();
        tcheight = 800;

            tcpack = d3.layout.pack()
            .sort(null)
            .size([tcwidth, tcheight + tcbleed * 2])
            .padding(2);

            tcsvg = d3.select("#results").append("svg")
            .attr("width", tcwidth)
            .attr("height", tcheight)
            .append("g")
            .attr("transform", "translate(0," + -tcbleed + ")");


    $.getJSON("../api/getWordCloud", { "researches" : reserchesstring , "limit" : limit , "language": language})
    .done(function(data){

        //console.log(data);


              var node = tcsvg.selectAll(".node")
                .data(tcpack.nodes(data)
                .filter(function(d) { return !d.children; }))
                .enter().append("g")
                .attr("class", "node")
                .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

              node.append("circle")
                .attr("class" , "tccircle")
                .attr("r", function(d) { return d.r; });

              node.append("text")
                .text(function(d) { return d.name; })
                .attr("class" , "tctext")
                .style("font-size", function(d) { var vv = (2 * d.r - 8) / this.getComputedTextLength() * 24; if(vv<0){vv=0;} var v = Math.min(2 * d.r, vv); if(v<0){v=0;} return v + "px"; })
                .attr("dy", ".35em");

        
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