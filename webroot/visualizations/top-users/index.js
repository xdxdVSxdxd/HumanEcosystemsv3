var researches;
var reserchesstring;
var mode = "month";

$( document ).ready(function() {
 
    reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

    if(getUrlParameter("mode")&&getUrlParameter("mode")!=mode){
        mode = getUrlParameter("mode");
        $("#modeselect").val( mode )
    }

    $("#modeselect").change(function(){
        mode = $("#modeselect").val();
        document.location = "./getviz?which=top-users&researches=" + reserchesstring + "&mode=" + mode;
    });

    getResults();

});


function exportdata(){

    //window.open("../api/getEmotions?researches=" + reserchesstring );

}

var shareLikeCoeff = 2;
var howmany = 20;

function getResults(){

        $.getJSON("../api/getTopSubjects", { "researches" : reserchesstring, "mode" : mode })
        .done(function(data){

            console.log("received data");

            data.results.forEach(function(d){
                d.followers = +d.followers;
                d.friends = +d.friends;
                d.nposts = +d.nposts;
                d.favorites = +d.favorites;
                d.shares = +d.shares;
                d.engagement = d.favorites + shareLikeCoeff*d.shares;
                d.reach = d.nposts*d.followers;
                d.epratio = 0;
                if(d.nposts!=0) { d.epratio = d.engagement / d.nposts; }
            });
            
            console.log("processed data");

            console.log(data);

            console.log("starting creating tables");

            //by followers
            data.results.sort(function(x, y){
               return d3.descending(x.followers, y.followers);
            })
            render("#results-followers" , data.results,howmany);

            console.log("1");

            //by friends
            data.results.sort(function(x, y){
               return d3.descending(x.friends, y.friends);
            })
            render("#results-friends" , data.results,howmany);

            console.log("2");

            //by nposts
            data.results.sort(function(x, y){
               return d3.descending(x.nposts, y.nposts);
            })
            render("#results-nposts" , data.results,howmany);

            console.log("2");

            //by reach
            data.results.sort(function(x, y){
               return d3.descending(x.reach, y.reach);
            })
            render("#results-reach" , data.results,howmany);

            console.log("3");

            //by engagement
            data.results.sort(function(x, y){
               return d3.descending(x.engagement, y.engagement);
            })
            render("#results-engagement" , data.results,howmany);

            console.log("4");

            //by engagement/npost ratio
            data.results.sort(function(x, y){
               return d3.descending(x.epratio, y.epratio);
            })
            render("#results-epratio" , data.results,howmany);

            console.log("5");

            console.log("finished");

        })
        .fail(function( jqxhr, textStatus, error ){
            //fare qualcosa in caso di fallimento
        }); 

}


function render(container , data, howmany){
    var content = "<table class='table'>";



        content = content + "<tr>";

            content = content + "<th>";
            content = content + "Image";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Nick";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Link";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Followers";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Friends";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "# Posts";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Favorites/Likes";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Shares";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Engagement";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Engagement / Posts Ratio";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Reach";
            content = content + "</th>";
            content = content + "<th>";
            content = content + "Content";
            content = content + "</th>";

        content = content + "</tr>";

    for(var i = 0; i<howmany && i<data.length; i++){
        content = content + "<tr>";

            content = content + "<td>";
            content = content + "<img src='" + data[i].imageurl + "' class='pimage' />";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "<strong>" + data[i].nick + "</strong>";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "<a href='" + data[i].purl + "' class='btn btn-default' target='_blank'><span class='glyphicon glyphicon-user' aria-hidden='true'></span> LINK</a>";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].followers + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].friends + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].nposts + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].favorites + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].shares + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].engagement + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].epratio + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "" + data[i].reach + "";
            content = content + "</td>";
            content = content + "<td>";
            content = content + "<a href='getviz?which=posts-per-user&researches=" + reserchesstring + "&subject_id=" + data[i].subject_id + "' class='btn btn-default' target='_blank'><span class='glyphicon glyphicon-th-list' aria-hidden='true'></span> LINK</a>";
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