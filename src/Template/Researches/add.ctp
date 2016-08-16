<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li><a href="listresearches">List Researches</a></li>
		  <li class="active">New Research</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<form class="form-horizontal" id="research-configurator" method="POST" action="add">
		  <h1>New Research</h1>
		  <div class="form-group">
		    <label for="research-name">Research Name</label>
		    <input type="text" class="form-control" id="research-name" name="research-name" placeholder="research name">
		  </div>
		  <div class="form-group">
		  	<h2>Research Elements</h2>
		  	<input type="hidden" id="parameters-list" name="parameters-list" value="" />
		    <table class="table table-bordered" id="parameters-summary-table">
		    	<tr>
		    		<th>Element type ID</th>
		    		<th>Element type</th>
		    		<th>Element parameters</th>
		    		<th>Element latitude</th>
		    		<th>Element longitude</th>
		    		<th>Element language</th>
		    		<th>Remove</th>
		    	</tr>
		    	<tbody></tbody>
		    </table>
		  </div>
		  <div class="well">
			  <div class="form-group">
			    <label for="research-element-type">Add Element</label>
			    <select class="form-control" id="research-element-type" name="research-element-type">
			    	<option value="-1">Select Element Type</option>
			    	<option value="-1">--------------------</option>
			    	<?php 
			    		foreach($research_element_types as $rt){
			    			echo("<option value='" . $rt->id . "'>" . $rt->label . "</option>");
			    		}
			    	?>
			    </select>
			  </div>
				<div class="form-group">
					<label for="research-element-parameter">Parameters</label>
					<input type="text" class="form-control" id="research-element-parameter" name="research-element-parameter" placeholder="parameter" aria-describedby="parameter-help-block">
					<span id="parameter-help-block" class="help-block">Insert parameters, separated by commas if more than one.</span>
				</div>
				<div class="form-group">
					<label for="research-element-lat">Latitude,Longitude</label>
					<input type="text" class="form-control" id="research-element-lat" name="research-element-lat" placeholder="" aria-describedby="latlng-help-block">
					<input type="text" class="form-control" id="research-element-lng" name="research-element-lng" placeholder="" aria-describedby="latlng-help-block">
					<span id="latlng-help-block" class="help-block">Insert latutude and longitude, one per field. Leave blank if not needed (will be replaced by the '-999' value).</span>
				</div>
				<div class="form-group">
					<label for="research-element-language">Language</label>
					<input type="text" class="form-control" id="research-element-language" name="research-element-language" placeholder="" aria-describedby="language-help-block">
					<span id="language-help-block" class="help-block">Insert <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">ISO code of language</a> in which you wish to search for. Leave blank if not needed (will be replaced by the 'XXX' value).</span>
				</div>
				<div class="form-group">
					<a href="javascript:addParameter();" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Parameter</a>
				</div>
			</div>
		  <a href="javascript:submitform();" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> Save</a>
		</form>
	</div>
</div>
<script>
	var i = 0;
	
	var replaceunderscore = "IIoOiOiOOiO9987";
	var replacebar = "mmMMmmMmM55645M";
	
	function addParameter(){
		var ptypeID = $("#research-element-type").val();
		var ptypeLabel = $("#research-element-type option:selected").text();
		var pvalue = $("#research-element-parameter").val();
		var plat = $("#research-element-lat").val();
		var plng = $("#research-element-lng").val();
		var planguage = $("#research-element-language").val();
		
		pvalue = pvalue.trim();
		plat = plat.trim();
		plng = plng.trim();
		if(plat==""){ plat = "-999"; }
		if(plng==""){ plng = "-999"; }
		planguage = planguage.trim();
		planguage = planguage.substring(0,3);
		if(planguage==""){ planguage="XXX"; }
		$("#research-element-language").val(planguage);


		if(ptypeID!=-1 && pvalue!=""){
			$("#parameters-summary-table > tbody:last-child").append("<tr id='p" + i + "'><td>" + ptypeID + "</td><td>" + ptypeLabel + "</td><td>" +  pvalue +  "</td><td>" +  plat +  "</td><td>" +  plng +  "</td><td>" +  planguage +  "</td><td><a href='javascript:removeparameter(\"p" + i + "\")'>X</a></td></tr>");
			i++;
		} else {
			alert("Please select an element type from the drop down menu, and provide at least one parameter");
		}
	}
	function removeparameter(which){
		$('#' + which).remove();
	}
	function submitform(){
		var rname = $("#research-name").val();
		rname = rname.trim();
		if(rname!=""){
			var pvlist = "";
			$("#parameters-summary-table tbody tr").each(function(i){
				var part = "";
				$(this).children('td').each(function(j){
					if(j==0 || j>=2){
						var tt = $(this).text();
						tt = tt.split("|").join(replacebar);//   replace("|","uudyUu7*6yYu")
						part = part + tt + "|";
					}
				});
				part = part.split("_").join(replaceunderscore); //part.replace("_","53193iI08*7");
				pvlist = pvlist + part + "_";
			});
			$("#parameters-list").val(pvlist);
			$("#research-configurator").submit();
		} else {
			alert("Research name cannot be empty");
		}

	}
</script>