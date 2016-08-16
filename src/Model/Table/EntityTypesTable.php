<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class EntityTypesTable extends Table
{
	public function initialize(array $config)
    {
		$this->hasMany('Entities' , ['dependent' => true , 'cascadeCallbacks' => false ]);
	}
}
?>