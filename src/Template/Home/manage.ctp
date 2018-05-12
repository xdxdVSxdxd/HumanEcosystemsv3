<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li class="active">Visualize</li>
		</ol>
	</div>
</div>
<?php
	if(isset($result)){
	?>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-warning" role="alert">Result: <?php echo($result); ?></div>
			</div>
		</div>
	<?php
	}
?>
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
		<h3>With selected researches:</h3>
		<a href="javascript:del();" class="btn btn-default"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete</a>
		<a href="javascript:exp();" class="btn btn-default"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export</a>
	</div>
</div>


<script>
	
	function del(){
		var selectedresearches = $("#research-list option:selected").map(function(){ return this.value }).get().join(",");
		if(selectedresearches==""){
			alert("select at least a research to visualize");
		} else {
			var baseurl = "<?php  
				echo ( $this->Url->build(
						[
							"controller" => "Home",
							"action" => 'manage'
						], true
					) );

			?>";

			var urlo = baseurl + "?which=" + selectedresearches + "&action=delete";
			document.location = urlo;
		}
	}

	function exp(){
		var selectedresearches = $("#research-list option:selected").map(function(){ return this.value }).get().join(",");
		if(selectedresearches==""){
			alert("select at least a research to visualize");
		} else {
			var baseurl = "<?php  
				echo ( $this->Url->build(
						[
							"controller" => "Home",
							"action" => 'manage'
						], true
					) );

			?>";

			var urlo = baseurl + "?which=" + selectedresearches + "&action=export";
			document.location = urlo;
		}
	}

</script>