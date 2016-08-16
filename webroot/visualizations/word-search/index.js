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



});


function exportdata(){

    var searchstring = $("#search").val();
    searchstring = searchstring.trim();
    if(searchstring.length<3){
        alert("Type at least 3 letters in the search box to export data");
    } else {
        window.open("../api/getContentMatch?researches=" + reserchesstring + "&q=" + searchstring);
    }

}

function doSearch(){

    var searchstring = $("#search").val();
    searchstring = searchstring.trim();
    if(searchstring.length<3){
        alert("Type at least 3 letters in the search box to perform the search");
    } else {
        $.getJSON("../api/getContentMatch", { "researches" : reserchesstring , "limit" : limit , "language": language , "q": searchstring })
        .done(function(data){

            d3.select("#results").html("");

            var result = d3.select("#results").append("div")
                .attr('class','resultsDiv');


            var resultItem = result.selectAll(".resultItem")
                .data(data.results)
                .enter()
                .append("div")
                    .attr("class", "resultItem");

            resultItem.append("div")
                .attr("class","resultContent")
                    .text(function(d){  return d.content;  });
            resultItem.append("div")
                .attr("class","resultMeta")
                .html( function(d){
                        //console.log(d);
                        return "<div class='row'>" +
                                "<div class='col-md-3'><a href='" + d.link + "'class='btn btn-default'><span class='glyphicon glyphicon-link' aria-hidden='true'></span> LINK</a></div>" +
                                "<div class='col-md-3'>" + d.created_at + "</div>" +
                                "<div class='col-md-3'>Coordinates: " + ((d.lat!=-999&&d.lng!=-999)?(  "(" + d.lat + "," + d.lng + ")"  ):"no coordinates") + "</div>" +
                                "<div class='col-md-3'>Energy/Comfort: " + "(" + d.energy + "," + d.comfort + ")" +  "</div>" +
                            "</div>";
                } );

        })
        .fail(function( jqxhr, textStatus, error ){
            //fare qualcosa in caso di fallimento
        });
    }
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