<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class EmotionsTable extends Table
{
	public function initialize(array $config)
    {
    	$this->belongsTo('Researches');
    	$this->belongsTo('ResearchElements');
    	$this->belongsTo('Contents');
    	$this->hasOne('EmotionTypes' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    }
}
?>