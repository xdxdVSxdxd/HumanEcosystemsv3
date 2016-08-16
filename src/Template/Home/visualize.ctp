<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li class="active">Visualize</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h1>Choose Researches to visualize</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<select id="research-list" class="form-control" multiple size="10">
			<?php
				foreach($researchlist as $r){
					echo("<option value='" . $r->id . "'>" . $r->name . "</option>");
				}
			?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h1>Choose Visualization</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="row rowhighmargin">
			<?php
				$i = 0;
				foreach ($visualizations as $viz) {

					/*
					$urlo = $this->Url->build(
						[
							"controller" => "Home",
							"action" => 'getviz',
							"?" => ["which" => $viz]
						], true
					);
					*/

					$urlo = 'javascript:openviz("' . $viz . '");';

					$image = $this->Html->image(
						"../visualizations/" . $viz . "/screenshot.png", 
						[
							//'fullBase' => true,
							'class' => 'img-responsive'
						]
					);

					$content = "<a href='" . $urlo . "'><div class='col-md-3 linkviz'><div class='vizcont'><div class='vizname'>" . $viz . "</div>" . $image . "</div></div></a>";

					echo($content);

					$i++;
					if($i==4){
						$i = 0;
						?>
							</div>
							<div class="row rowhighmargin">
						<?php
					}
				}

			?>
		</div>
	</div>
</div>

<script>
	
	function openviz(viz){
		var selectedresearches = $("#research-list option:selected").map(function(){ return this.value }).get().join(",");
		if(selectedresearches==""){
			alert("select at least a research to visualize");
		} else {
			var baseurl = "<?php  
				echo ( $this->Url->build(
						[
							"controller" => "Home",
							"action" => 'getviz'
						], true
					) );

			?>";

			var urlo = baseurl + "?which=" + viz + "&researches=" + selectedresearches;
			document.location = urlo;
		}
	}

</script>