<?php 

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

	foreach($cssfiles as $css){
		echo ( $this->Html->css("../visualizations/" . $viz . "/" . $css)  );	
	}
	
?>
<div class="row">
	<div class="col-md-12">
		<div id="visualization-holder">

		<?php 

			$dir = new Folder( './visualizations/' . $viz );

			$files = $dir->find('.*\.ctp', true);

			foreach($files as $f){

				$file = new File($dir->pwd() . DS . $f);

				echo($file->read());

			}

			

		?>
			
		</div>
	</div>
</div>
<?php

	foreach($jsfiles as $js){
		echo ( $this->Html->script("../visualizations/" . $viz . "/" . $js  )  );
	}

	
?>