<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../../">Home</a></li>
		  <li><a href="../listresearches">List Researches</a></li>
		  <li class="active">View Research</li>
		</ol>
	</div>
</div>
<div class="row well">
	<div class="col-md-12">
		<h1><?php echo( strtoupper( $research_name)  ); ?></h1>
		<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#research-configuration" aria-expanded="false" aria-controls="collapseExample">
		  <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configure
		</button>

		<div class="well-padded">
			<div class="collapse" id="research-configuration">


				<ul class="nav nav-tabs" role="tablist">
				    <li role="presentation" class="active"><a href="#socialconfiguration" aria-controls="socialconfiguration" role="tab" data-toggle="tab">Social Networks</a></li>
				    <li role="presentation"><a href="#researchelements" aria-controls="researchelements" role="tab" data-toggle="tab">Research Elements</a></li>
				  </ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="socialconfiguration">
						<form class="form-horizontal" id="research-configurator" method="POST" action="addconfiguration">
							<input type="hidden" name="research_id" id="research_id" value="<?php echo($research_id); ?>" />
							<div class="row">
								<div class="col-md-4">
									<h3>Twitter</h3>
									<p>Please insert your Twitter developer parameters which you can find <a href="https://apps.twitter.com/" target="_blank">HERE</a></p>
									<div class="form-group">
										<label for="twitter_consumer_key">Twitter Consumer Key</label>
										<input type="text" class="form-control" id="twitter_consumer_key" name="twitter_consumer_key" value="<?php echo($twitter_consumer_key); ?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="twitter_consumer_secret">Twitter Consumer Secret</label>
										<input type="text" class="form-control" id="twitter_consumer_secret" name="twitter_consumer_secret" value="<?php echo($twitter_consumer_secret); ?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="twitter_token">Twitter Token</label>
										<input type="text" class="form-control" id="twitter_token" name="twitter_token" value="<?php echo($twitter_token); ?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="twitter_token_secret">Twitter Token Secret</label>
										<input type="text" class="form-control" id="twitter_token_secret" name="twitter_token_secret" value="<?php echo($twitter_token_secret); ?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="twitter_bearer_token">twitter_bearer_token</label>
										<input type="text" class="form-control" id="twitter_bearer_token" name="twitter_bearer_token" value="<?php echo($twitter_bearer_token); ?>" placeholder="" aria-describedby="parameter-help-block">
										<span id="parameter-help-block" class="help-block">Insert the other Twitter parameters to get the Bearer Token(<span id="bearer-holder"></span>)</span>
									</div>
								</div>
								<div class="col-md-4">
									<h3>Instagram</h3>
									<p>Please insert your Instagram developer parameters which you can find <a href="https://www.instagram.com/developer/clients/manage/" target="_blank">HERE</a></p>
									<div class="form-group">
										<label for="insta_client_id">Instagram Client ID</label>
										<input type="text" class="form-control" id="insta_client_id" name="insta_client_id"  value="<?php echo($insta_client_id); ?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="insta_token">Instagram App Token</label>
										<input type="text" class="form-control" id="insta_token" name="insta_token" value="<?php echo($insta_token); ?>" placeholder="" aria-describedby="insta-help-block">
										<span id="insta-help-block" class="help-block">To get your Instagram App Token insert the Client ID and click the link which will appear HERE(<span id="insta-link-holder"></span>)</span>
									</div>
								</div>
								<div class="col-md-4">
									<h3>Facebook</h3>
									<p>Please insert your Facebook developer parameters which you can find <a href="https://developers.facebook.com/apps/" target="_blank">HERE</a></p>
									<div class="form-group">
										<label for="fb_app_id">Facebook App ID</label>
										<input type="text" class="form-control" id="fb_app_id" name="fb_app_id" value="<?php echo($fb_app_id); ?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="fb_app_secret">Facebook App Secret</label>
										<input type="text" class="form-control" id="fb_app_secret" name="fb_app_secret" value="<?php echo($fb_app_secret); ?>" placeholder="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<a href="javascript:saveAll(true);" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> Save Social Configuration</a>
								</div>
							</div>
						</form>
					</div>
					<div role="tabpanel" class="tab-pane" id="researchelements">
						<div class="row">
							<div class="col-md-12">
								<!-- id, research_element_type_id, content, lat, lng, language, active -->
								<table class="table table-bordered" id="researchelementstable">
									<thead>
									<tr>
										<th class="col-md-1">ID</th>
										<th class="col-md-2">Element Type</th>
										<th class="col-md-3">Parameters</th>
										<th class="col-md-2">Geo Search</th>
										<th class="col-md-1">Language</th>
										<th class="col-md-1">Active</th>
										<th class="col-md-2">Commands</th>
									</tr>
									</thead>
									<tbody>
									<?php

										foreach($researchelements as $re){
											?>

											<tr id="rowelement-<?php  echo(  $re->id ); ?>">
												<td><?php  echo(  $re->id ); ?></td>
												<td><?php  
													$label = "";
													foreach ($research_element_types as $v) {
														if($v->id==$re->research_element_type_id){
															$label = $v->label;
														}
													}
													echo(  $label ); 

												?></td>
												<td><?php  echo(  $re->content ); ?></td>
												<td><?php  echo(  $re->lat . "," . $re->lng ); ?></td>
												<td><?php  echo(  $re->language ); ?></td>
												<td><input type="checkbox" class="re-toggle" id="activate-re-<?php echo($re->id); ?>"
													<?php  if($re->active==1){echo("checked");} ?> 
												data-toggle="toggle"></td>
												<td><a href='javascript:deleteelement(<?php  echo(  $re->id ); ?>)' class='btn btn-default'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> DELETE</a></td>
											</tr>

											<?php
										}
									?>
									<form class="form-horizontal" id="research-configurator" method="POST" action="addreselement">
									<input type="hidden" id="research_id" name="research_id" value="<?php echo($research_id); ?>" />
									<input type="hidden" id="parameters-list" name="parameters-list" value="" />
									<tr class="warning">
										<td>New Element</td>
										<td>
											<div class="form-group">
												<label for="research-element-type">Select Type</label>
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
										</td>
										<td>
											<div class="form-group">
												<label for="research-element-parameter">Parameters</label>
												<input type="text" class="form-control" id="research-element-parameter" name="research-element-parameter" placeholder="parameter" aria-describedby="parameter-help-block">
												<span id="parameter-help-block" class="help-block">Insert parameters, separated by commas if more than one.</span>
											</div>
										</td>
										<td>
											<div class="form-group">
												<label for="research-element-lat">Latitude,Longitude</label>
												<input type="text" class="form-control" id="research-element-lat" name="research-element-lat" placeholder="" aria-describedby="latlng-help-block">
												<input type="text" class="form-control" id="research-element-lng" name="research-element-lng" placeholder="" aria-describedby="latlng-help-block">
												<span id="latlng-help-block" class="help-block">Insert latutude and longitude, one per field. Leave blank if not needed (will be replaced by the '-999' value).</span>
											</div>
										</td>
										<td>
											<div class="form-group">
												<label for="research-element-language">Language</label>
												<input type="text" class="form-control" id="research-element-language" name="research-element-language" placeholder="" aria-describedby="language-help-block">
												<span id="language-help-block" class="help-block">Insert <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">ISO code of language</a> in which you wish to search for. Leave blank if not needed (will be replaced by the 'XXX' value).</span>
											</div>
										</td>
										<td></td>
										<td>
											<div class="form-group">
												<a href="javascript:addParameter();" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Parameter</a>
											</div>
										</td>
									</tr>
									</form>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="container-boxed">
			<span><strong>Content:</strong><?php echo($tot_contents); ?></span>
			<span><strong>Subjects:</strong><?php echo($tot_subjects); ?></span>
			<span><strong>Relations:</strong><?php echo($tot_relations); ?></span>
			<span><strong>Emotions:</strong><?php echo($tot_emotions); ?></span>
		</div>
	</div>
</div>

<script>

var element_types = new Array();
<?php 
foreach ($research_element_types as $v) {
	?>
	element_types[<?php  echo($v->id); ?>] = '<?php  echo($v->label); ?>';
	<?php
}
?>

$( document ).ready(function() {
 
 $("#twitter_consumer_key").change(function(){
 	getBearerLink();
 });

 $("#twitter_consumer_secret").change(function(){
 	getBearerLink();
 });

 $("#twitter_token").change(function(){
 	getBearerLink();
 });

 $("#twitter_token_secret").change(function(){
 	getBearerLink();
 });

 $("#insta_client_id").change(function(){
 	getInstaToken();
 });
 
 $("#insta_token").change(function(){
 	getInstaToken();
 });

 if($("#insta_client_id").val()!=""){
 	getInstaToken();
 }


 $(".re-toggle").change(applytoggle);

});


function deleteelement(id){
	if(confirm("Are you sure? Deleting the element also implies the removal of all the data associated to it.")){
		// TODO cancellare l'elemento dal db con il controller e dalla table con un remove del <tr> con ID=rowelement-ID
		$.getJSON( 
			"../../researches/deleteresearchelement",
			{
				"id": id
			}, function(data){

				if(data!=null  && data.length==1 && data[0].status=="success"){
					// ha funzionato
					$("#rowelement-" + id).remove();
				} else {
					// non ha funzionato
					alert("The element could not be removed. Please try later or alert service.");
				}
				
			}

		);
	}
}


var applytoggle = function( e ){

		var id = $(e.target).attr("id");
 		var idn = id.replace("activate-re-","");
 		var state = 0;
 		if($(e.target).is(":checked")){
 			state = 1;
 		}

 		$.getJSON( 
			"../../researches/toggleresearchelementactive",
			{
				"id": idn,
				"state": state
			}, function(data){
				
				if(data!=null && data.length==1 && data[0]=="success"){
					// ha funzionato
				} else {
					// non ha funzionato
					//ripristinare
					if(  state==0  ){
						$("#" + id).prop('checked',true);
					} else {
						$("#" + id).prop('checked',false);
					}
				}
				
			}

		);
 		
 }

$("#research-configurator input").change(function(){
	saveAll(false);
});

function saveAll( showAlert ){
	var twconsumerkey = $("#twitter_consumer_key").val();
	var twconsumersecret = $("#twitter_consumer_secret").val();
	var twtoken = $("#twitter_token").val();
	var twtokensecret = $("#twitter_token_secret").val();
	var twbearertoken = $("#twitter_bearer_token").val();

	var instaclientid = $("#insta_client_id").val();	
	var instaapptoken = $("#insta_token").val();	

	var fbappid = $("#fb_app_id").val();	
	var fbapsecret = $("#fb_app_secret").val();

	$.getJSON( 
		"../../researches/updateresearchconfig/<?php echo($research_id); ?>",
		{
			"twconsumerkey": twconsumerkey,
			"twconsumersecret": twconsumersecret,
			"twtoken": twtoken,
			"twtokensecret": twtokensecret,
			"twbearertoken": twbearertoken,
			"instaclientid": instaclientid,
			"instaapptoken": instaapptoken,
			"fbappid": fbappid,
			"fbapsecret": fbapsecret 
		}, function(data){
			//
			
		}

	);

	if(showAlert){
		alert("configuration saved!");
	}
}

function getBearerLink(){

	var twconsumerkey = $("#twitter_consumer_key").val();
	var twconsumersecret = $("#twitter_consumer_secret").val();
	var twtoken = $("#twitter_token").val();
	var twtokensecret = $("#twitter_token_secret").val();
	var twbearertoken = $("#twitter_token_secret").val();

	var link = "";

	if(twconsumerkey!="" && twconsumersecret!="" && twtoken!="" && twtokensecret!="" ){
		link = "<a href='../../utilities/get-twitter-bearer-token?tw-consumer-key=" + twconsumerkey + "&tw-consumer-secret=" + twconsumersecret + "&tw-token=" + twtoken + "&tw-token-secret=" + twtokensecret + "' target='_blank'>Get Bearer Token</a>"
	}

	$("#bearer-holder").html(link);

}


function getInstaToken(){

	var instaclientid = $("#insta_client_id").val();
	instaclientid = instaclientid.trim();

	if(instaclientid!=""){

		var instaurl = "https://api.instagram.com/oauth/authorize/?client_id=" + instaclientid + "&redirect_uri=<?php echo($rediruri); ?>&response_type=token";
		$("#insta-link-holder").html( "<a href='" + instaurl +"' target='_blank'>HERE</a>" );

	}

}


// add research element start
	var i = 0;
	
	var replaceunderscore = "<?php echo($replaceunderscore); ?>";
	var replacebar = "<?php echo($replacebar); ?>";

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
			

			$.getJSON( 
				"../../researches/addresearchelement",
				{
					"ptypeID": ptypeID,
					"pvalue": pvalue,
					"plat": plat,
					"plng": plng,
					"planguage": planguage,
					'research_id': <?php  echo( $research_id ); ?>,
					'user_id': <?php  echo( $user_id ); ?>
				}, function(data){
					
					// do something with data
					if(data[0].status=="success"){

						$("#researchelementstable > tbody:last-child").append("<tr id='rowelement-" + data[0].id + "'><td>" + data[0].id + "</td><td>" + element_types[ data[0].research_element_type_id ] + "</td><td>" + data[0].content + "</td><td>" + data[0].lat + "," + data[0].lng + "</td><td>" + data[0].language + "</td><td>" + "<input type='checkbox' class='re-toggle' id='activate-re-" + data[0].id + "'" + (data[0].active==1?" checked ":"") + "data-toggle='toggle'>" + "</td><td>" + "<a href='javascript:deleteelement(" + data[0].id + ")' class='btn btn-default'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> DELETE</a>" + "</td></tr>");

						$("#activate-re-" + data[0].id ).bootstrapToggle();
						$("#activate-re-" + data[0].id ).change(applytoggle);

					} else {
						alert("Adding element failed! Check your input or try later.");
					}
					
				}

			);



		} else {
			alert("Please select an element type from the drop down menu, and provide at least one parameter");
		}
	}

	function removeparameter(which){
		$('#' + which).remove();
	}

// add research element end


</script>