<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class SubjectsTable extends Table
{
	public function initialize(array $config)
    {
    	$this->belongsTo('Researches');
    	$this->belongsTo('ResearchElements');
    }
}
?>