<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class ResearchesTable extends Table
{
	public function initialize(array $config)
    {
    	$this->belongsTo('Users');
    	$this->hasMany('Contents' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    	$this->hasMany('Emotions' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    	$this->hasMany('ResearchElements' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    	$this->hasMany('Subjects' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    	$this->hasMany('Relations' , ['dependent' => true , 'cascadeCallbacks' => false ]);
    }
}
?>