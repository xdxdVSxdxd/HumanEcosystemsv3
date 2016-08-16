<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		</ol>
	</div>
</div>
<div class="row well">
	<div class="col-md-4"><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php echo($user_name); ?></h1></div>
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-12">
				<?php
					echo $this->Html->link(
					    '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Research',
					    '/researches/add',
					    array('class' => 'btn btn-default', 'role' => 'button', 'escape' => false)
					);
				?>

				<?php
					echo $this->Html->link(
					    '<span class="glyphicon glyphicon-lamp" aria-hidden="true"></span> Logout',
					    '/users/logout',
					    array('class' => 'btn btn-default', 'role' => 'button', 'escape' => false)
					);
				?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h2>Researches</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php
			foreach($researchlist as $r){
				?>
				<div class="col-md-3">
					<h3><?php echo($r["name"]); ?></h3>
					<a href="javascript:viewresearch(<?php  echo($r["id"]) ?>);"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> view</a>
					<!--a href="javascript:editresearch(<?php  echo($r["id"]) ?>);"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> edit</a-->
					<a href="javascript:deleteresearch(<?php  echo($r["id"]) ?>);"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> delete</a>
				</div>

				<?php
			}
		?>
	</div>
</div>
<script>
	function viewresearch(id){
		document.location = "../researches/view/" + id;
	}

	function editresearch(id){
		document.location = "../researches/edit/" + id;
	}

	function deleteresearch(id){

		if(confirm("Do you really want to delete this research?")){
			document.location = "../researches/delete/" + id;
		}
		
	}
</script>