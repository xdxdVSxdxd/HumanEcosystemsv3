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


    getSentiment();

});

function exportdata(){

    window.open("../api/getSentiment?researches=" + reserchesstring );

}


function getSentiment(){

    
    $.getJSON("../api/getSentiment", { "researches" : reserchesstring , "limit" : limit})
    .done(function(data){

        console.log(data);

        var positive = +data.positive;
        var negative = +data.negative;
        var neutral = +data.neutral;
            
        var total = positive + neutral + negative;

        var perc_positive = 0;
        var perc_negative = 0;
        var perc_neutral = 0;  

        if(total!=0){
            perc_positive = 100*positive/total;
            perc_negative = 100*negative/total;
            perc_neutral = 100*neutral/total;
        }

        $("#positive").text(   perc_positive.toFixed(2)  + "%");
        $("#neutral").text(   perc_neutral.toFixed(2)  + "%");
        $("#negative").text(   perc_negative.toFixed(2)  + "%");
        
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