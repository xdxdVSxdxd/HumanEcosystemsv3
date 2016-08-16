<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

class ContentsEntitiesTable extends Table
{
	public function initialize(array $config)
    {
    	$this->belongsTo('Contents');
        $this->belongsTo('Entities');
    }
}
?>