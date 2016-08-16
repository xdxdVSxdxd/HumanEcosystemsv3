<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li class="active">Add User</li>
		</ol>
	</div>
</div>
<div class="row users form">
<?= $this->Form->create($user) ?>
    <fieldset>
    <div class="row">
    	<div class="col-md-12">
        	<h1><?= __('Add User') ?></h1>
        </div>
    </div>
    <div class="row">
    	<div class="col-md-12">
    		<div class="form-group">
        		<?= $this->Form->input('username', ['class' => 'form-control']) ?>
        	</div>
        </div>
    </div>
    <div class="row">
    	<div class="col-md-12">
    		<div class="form-group">
        		<?= $this->Form->input('password', ['class' => 'form-control']) ?>
        	</div>
        </div>
    </div>
    <div class="row">
    	<div class="col-md-12">
    		<div class="form-group">
		        <?= $this->Form->input('role', [
		            'options' => ['admin' => 'Admin', 'author' => 'Author'],
		            'class' => 'form-control'
		        ]) ?>
		    </div>
	    </div>
   </div>
   </fieldset>
   <div class="row">
    	<div class="col-md-12">
    		<div class="form-group">
				<?= $this->Form->button(  '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' .  __('Submit'), ['class' => 'btn btn-default']); ?>
			</div>
		</div>
	</div>
<?= $this->Form->end() ?>
</div>