<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li class="active">Login</li>
		</ol>
	</div>
</div>
<?= $this->Flash->render('auth') ?>
<div class="row users form">
<?= $this->Form->create() ?>
    <fieldset>
    <div class="row">
    	<div class="col-md-12">
        	<h1><?= __('Please enter your username and password') ?></h1>
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
   </fieldset>
   <div class="row">
    	<div class="col-md-12">
    		<div class="form-group">
				<?= $this->Form->button(  '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' .  __('Login'), ['class' => 'btn btn-default']); ?>
			</div>
		</div>
	</div>
<?= $this->Form->end() ?>
</div>