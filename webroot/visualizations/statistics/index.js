var researches;
var reserchesstring;

$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");


    getStatistics();
    
});

function exportstats(){

    window.open("../api/getStatisticsOnResearches?researches=" + reserchesstring );

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

function getStatistics(){


  $.getJSON("../api/getStatisticsOnResearches", { "researches" : reserchesstring })
    .done(function(data){

      console.log(data);

      data.results.forEach(function(d) {
        d.comfort = +d.comfort;
        d.energy = +d.energy;
        d.number = +d.number;
      });

      var content = "";
      content = content + "<table class='table table-hover'>";

      content = content + "<tr>";
      content = content + "<th>Research Name</th>";
      content = content + "<th>Element</th>";
      content = content + "<th>Type</th>";
      content = content + "<th>From</th>";
      content = content + "<th>To</th>";
      content = content + "<th>Comfort (avg)</th>";
      content = content + "<th>Energy (avg)</th>";
      content = content + "<th>Quantity</th>";
      content = content + "</tr>";

      var total = 0;
      var totalperresearch = 0;

      var cr = "";
      var isfirst = true;

      for(var i = 0; i<data.results.length; i++){
        var d = data.results[i];

        var showname = true;

        if (d.research_name!=cr){
          cr = d.research_name;
          if(isfirst){
            isfirst = false;
          } else{
            // mettere totale
            content = content + "<tr class='warning'>";
            content = content + "<td colspan='7'><strong><em>TOTAL For this research</em></strong></td>";
            content = content + "<td><strong><em>" + totalperresearch +  "</em></strong></td>";
            content = content + "</tr>";
          }
          totalperresearch = 0;
        } else {
          showname = false;
        }


        totalperresearch = totalperresearch + d.number;
        total = total + d.number;

        content = content + "<tr " + ( (d.number==0?" class='danger'":"") ) + ">";
        content = content + "<td><strong>" + (showname?d.research_name:"") + "</strong></td>";
        content = content + "<td>" + d.research_element.replace(/,/g , ", ") +  "</td>";
        content = content + "<td>" + d.research_element_type + "</td>";
        content = content + "<td>" + d.from_date + "</td>";
        content = content + "<td>" + d.to_date + "</td>";
        content = content + "<td>" + d.comfort + "</td>";
        content = content + "<td>" + d.energy + "</td>";
        content = content + "<td>" + d.number +  "</td>";
        content = content + "</tr>";

      }

      if(data.results.length>0){
            content = content + "<tr class='warning'>";
            content = content + "<td colspan='7'><strong><em>TOTAL For this research</em></strong></td>";
            content = content + "<td><strong><em>" + totalperresearch +  "</em></strong></td>";
            content = content + "</tr>";
      }

      content = content + "<tr class='info'>";
      content = content + "<td colspan='7'>GRAND TOTAL</td>";
      content = content + "<td>" + total +  "</td>";
      content = content + "</tr>";


      content = content + "</table>";
      
      $("#results").html(content);


    })
    .fail(function( jqxhr, textStatus, error ){
        //fare qualcosa in caso di fallimento
    });


}