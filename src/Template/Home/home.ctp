<div class="row">
	<div class='col-md-12'>
		<h1>Welcome!</h1>
	</div>
</div>
<div class="row">
	<div class='col-md-12'>
		<?php
			if(  !$authed  ){
			        // Logged in
				?>
				<a href="users/login" class="btn btn-default"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Login</a>
				<?php
			} else {
				?>
				<a href="researches/listresearches" class="btn btn-default"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> View your researches</a>
				<?php
			}
		?>

		<a href="home/visualize" class="btn btn-default"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Visualize</a>

		<a href="users/add" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add User</a>

		<?php
			if(  $authed  ){
			        // Logged in
				?>
				<a href="users/logout" class="btn btn-default"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Logout</a>
				<?php
			}
		?>
	</div>
</div>