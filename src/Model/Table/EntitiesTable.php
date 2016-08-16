<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class EntitiesTable extends Table
{
	public function initialize(array $config)
    {
    	$this->entityClass('App\Model\Entity\Entita');
    	$this->hasOne('EntityTypes');
    	$this->belongsToMany('Contents', ['through' => "ContentsEntities"]);
    }
}
?>