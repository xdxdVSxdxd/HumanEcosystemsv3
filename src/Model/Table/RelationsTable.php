<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class RelationsTable extends Table
{
	public function initialize(array $config)
    {
    	$this->hasMany('Subjects1' , [
    		'className' => 'Subjects',
    		'property_name' => 'subject_1_id'
    	]);

    	$this->hasMany('Subjects2' , [
    		'className' => 'Subjects',
    		'property_name' => 'subject_2_id'
    	]);

    	$this->belongsTo('Researches');
    	$this->belongsTo('ResearchElements');

    }
}
?>
