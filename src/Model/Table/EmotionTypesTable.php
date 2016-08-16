<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class EmotionTypesTable extends Table
{
	public function initialize(array $config)
    {
		$this->hasMany('Emotions' , ['dependent' => true , 'cascadeCallbacks' => false ]);
	}
}
?>