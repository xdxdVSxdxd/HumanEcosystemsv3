var researches;
var reserchesstring;

$( document ).ready(function() {
 
 	reserchesstring = getUrlParameter("researches");
    researches = reserchesstring.split(",");

   	// prepare your javascript environment here
   	// now that the document has loaded
   
   	// .. and then start to create your visualisation
    doSomething();

});


function doSomething(){
	// here is a nice place to put 
	// your visualisation logics

	// for example, let's 'visualise' a silly message:
	$("#results").html("<h2>It works!</h2>");
}

function exportdata(){

	// this function is called in the HTML template
	//
	// it handles the export of data from the visualisation
	// 
	// in the examples, you can see that the visualisations use
	// the endpoints of the Human Ecosystems API to get the data
	// to be visualised
	//
	// for example this one:
	// api/getEnergyComfortDistribution
	// which allows to get the emotional distribution of your research
	//
	// for example, to allow people to download the data, you might
	// imagine placing a link which opens up a window with the data in
	// it, without visualising it, as raw output from the API
	//
    // for example like this:
    //window.open("../api/getEnergyComfortDistribution?researches=" + reserchesstring );

}


// this is a simple utility to get parameters off the URL
// from the query string: you can use it to configure your visualisation
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