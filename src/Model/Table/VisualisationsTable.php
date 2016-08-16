<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;


class VisualisationsTable extends Table
{
	public function initialize(array $config)
    {
    }

    public function getVisualizations(){
		$result = array();

		/*
		$result[] = "prova1";
		$result[] = "prova2";
		$result[] = "prova3";
		$result[] = "prova4";
		$result[] = "prova5";
		$result[] = "prova6";
		$result[] = "prova7";
		$result[] = "prova8";
		*/

		$dir = new Folder('./visualizations/');

		$subdirs = $dir->read(true);

		foreach ($subdirs[0] as $subdir) {
			$result[] = $subdir;
		}



		return $result;
	}



	public function getCSSFilesList($viz){
		$result = array();

		/*
		$result[] = "prova1";
		$result[] = "prova2";
		$result[] = "prova3";
		$result[] = "prova4";
		$result[] = "prova5";
		$result[] = "prova6";
		$result[] = "prova7";
		$result[] = "prova8";
		*/

		$dir = new Folder('./visualizations/' . $viz . '/');

		$files = $dir->find('.*\.css', true);

		foreach ($files as $f) {
			$result[] = $f;
		}



		return $result;
	}


	public function getJSFilesList($viz){
		$result = array();

		/*
		$result[] = "prova1";
		$result[] = "prova2";
		$result[] = "prova3";
		$result[] = "prova4";
		$result[] = "prova5";
		$result[] = "prova6";
		$result[] = "prova7";
		$result[] = "prova8";
		*/

		$dir = new Folder('./visualizations/' . $viz . '/');

		$files = $dir->find('.*\.js', true);

		foreach ($files as $f) {
			$result[] = $f;
		}



		return $result;
	}


}
?>