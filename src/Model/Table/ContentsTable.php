<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class ContentsTable extends Table
{
	public function initialize(array $config)
    {
    	$this->belongsTo('Subjects');
    	$this->belongsTo('Researches');
    	$this->belongsTo('ResearchElements');
    	$this->belongsToMany('Entities', ['through' => 'ContentsEntities']);
    }
}
?>