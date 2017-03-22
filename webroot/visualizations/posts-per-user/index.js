var researches;
var reserchesstring;
var mode = "MONTH";
var subject_id = -1;

$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");
    subject_id = getUrlParameter("subject_id");

    getResults();

});


function exportdata(){

    //window.open("../api/getEmotions?researches=" + reserchesstring );

}

function getResults(){

        $.getJSON("../api/getPostsPerUserID", { "researches" : reserchesstring, "mode" : mode , "subject_id" : subject_id})
        .done(function(data){

            console.log(data);

            render("#results" , data.results, 500);


        })
        .fail(function( jqxhr, textStatus, error ){
            //fare qualcosa in caso di fallimento
        }); 

}


function render(container , data, howmany){
    var content = "<table class='table'>";



        content = content + "<tr>";

            content = content + "<th>";
            content = content + "Content";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Date";
            content = content + "</th>";

        content = content + "</tr>";

    for(var i = 0; i<howmany && i<data.length; i++){
        content = content + "<tr>";

            content = content + "<td>";
            content = content + "" + data[i].text + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "<strong>" + data[i].created_at + "</strong>";
            content = content + "</td>";

        content = content + "</tr>";
    }

    content = content + "</table>";

    $(container).html(content);
}

var getKeys = function(obj){
   var keys = [];
   for(var key in obj){
      keys.push(key);
   }
   return keys;
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