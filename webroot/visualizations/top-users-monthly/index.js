var researches;
var reserchesstring;


$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    getViz();

});


function exportdata(){

    window.open("../api/getEmotionsTimeline?researches=" + reserchesstring );

}

function getViz(){

    var margin = {top: 30, right: 20, bottom: 30, left: 50},
    width = $("#results").width() - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

    $.getJSON("../DumpedData/MonthlyTopUsers.json", 
      { 
        "researches" : reserchesstring
      }
    )
    .done(function(data){

      var contain = d3.select("#results");

      var i = 0;

      data.forEach(function(u,j){

        i++

        var userblock = contain.append("div")
          .attr("class", "userblock");

        userblock.append("p")
          .attr("class", "user-position")
          .text(i);

        var userblock2 = userblock.append("div")
                                  .attr("class","usersubcontainer");

        userblock2.append("img")
          .attr("src", u.profile_image_url)
          .attr("width" , 32)
          .attr("height", 32)
          .attr("border", 0);

        userblock2.append("p")
          .attr("class", "user-name")
          .text(u.name);

        var userblock3 = userblock.append("div")
                                  .attr("class","usersubcontainer");

        var userlink = userblock3.append("a")
          .attr("class", "user-nick")
          .attr("href",u.profile_url)
          .text(u.screen_name);


        userblock3.append("p")
          .attr("class", "user-count")
          .text(u.number);



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