<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class ResearchElementsTable extends Table
{
	public function initialize(array $config)
    {
    	$this->hasOne('ResearchElementTypes' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    	$this->hasMany('Relations' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    	$this->belongsTo('Researches');
    }
}
?>