var researches;
var reserchesstring;

var mode = "week";

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


$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    getData();
    
});


var exportdata;
function exportstats(){

    var content = "<strong>Groups</strong><br />";
    for(var i = 0; i<groups.length; i++){
      content = content + "[" + i + "]" + groups[i].name + "<br />";
    }

    content = content + "<br /><strong>Matrice delle adiacenze</strong><br />";
    content = content + "<table>";

    for(var i = 0; i<exportdata.length; i++){
      content = content + "<tr>";
      for(var j = 0; j<exportdata[i].length; j++){
        content = content + "<td>" + exportdata[i][j] + "</td>";
      }
      content = content + "</tr>";
    }

    content = content + "</table>";

    var win = window.open("", "Export", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=780, height=200, top="+(screen.height-400)+", left="+(screen.width-840));
    win.document.body.innerHTML = content;
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

function getData(){


  $.getJSON("../api/getSubjectsForGroups", { 
                                    "researches" : reserchesstring,
                                    "mode" : mode,
                                    "groups" : groups
                                  })
    .done(function(data){




      var nodes = [];
      var links = [];

      var matrix = [];

      var grouptoindex = new Object();

      for(var i = 0; i<groups.length; i++){
        matrix[i] = [];
        grouptoindex[groups[i].slug] = i;
        for(var j = 0; j<groups.length; j++){
          matrix[i][j] = 0;
        }        
      }

      var nest1 = d3.nest()
                    .key( function(d){ return d.s1id; })
                    .entries( data.results );

      


      for(var i = 0; i<nest1.length; i++){
        var d = nest1[i];
        //console.log(d);
        var value = [];
        for(var y = 0; y<groups.length; y++ ){
          value[groups[y].slug] = 0;
        }
        for(var z = 0; z<d.values.length; z++ ){
          for(var y = 0; y<groups.length; y++ ){
            d.values[z][groups[y].slug] = +d.values[z][groups[y].slug];
            value[groups[y].slug] = value[groups[y].slug] + d.values[z][groups[y].slug];
          }  
        }

        //console.log(value);

        for(var j = 0; j<groups.length; j++){
          var id1 = grouptoindex[groups[j].slug];
          if(value[groups[j].slug]==1){
            for(var k = 0; k<groups.length; k++){
              var id2 = grouptoindex[groups[k].slug];
              if(value[groups[k].slug]==1){
                matrix[id1][id2] = matrix[id1][id2] + 1;
              }
            }
          }
        }
      }

      exportdata = matrix;

      console.log(matrix);

      var outerRadius = 960 / 2,
      innerRadius = outerRadius - 130;

      var fill = d3.scale.category20c();

      var chord = d3.layout.chord()
      .padding(.04)
      .sortSubgroups(d3.descending)
      .sortChords(d3.descending);

      var arc = d3.svg.arc()
      .innerRadius(innerRadius)
      .outerRadius(innerRadius + 20);

      var svg = d3.select("#results").append("svg")
      .attr("width", outerRadius * 2)
      .attr("height", outerRadius * 2)
      .append("g")
      .attr("transform", "translate(" + outerRadius + "," + outerRadius + ")");
      
      chord.matrix(matrix);

      var g = svg.selectAll(".group")
        .data(chord.groups)
        .enter().append("g")
        .attr("class", "group");

      g.append("path")
        .style("fill", function(d) { return fill(d.index); })
        .style("stroke", function(d) { return fill(d.index); })
        .attr("d", arc);

      g.append("text")
        .each(function(d) { d.angle = (d.startAngle + d.endAngle) / 2; })
        .attr("dy", ".35em")
        .attr("transform", function(d) {
          return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")"
            + "translate(" + (innerRadius + 26) + ")"
            + (d.angle > Math.PI ? "rotate(180)" : "");
        })
        .style("text-anchor", function(d) { return d.angle > Math.PI ? "end" : null; })
        .text(function(d) { 
          return groups[d.index].name;  //  nameByIndex.get(d.index); 
        });

      svg.selectAll(".chord")
        .data(chord.chords)
        .enter().append("path")
        .attr("class", "chord")
        .style("stroke", function(d) { return d3.rgb(fill(d.source.index)).darker(); })
        .style("fill", function(d) { return fill(d.source.index); })
        .attr("d", d3.svg.chord().radius(innerRadius));

    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });


}