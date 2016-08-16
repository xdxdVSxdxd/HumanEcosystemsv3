<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class ResearchElementTypesTable extends Table
{
	public function initialize(array $config)
    {
		$this->hasMany('ResearchElements' , ['dependent' => true , 'cascadeCallbacks' => false ]);
	}
}
?>