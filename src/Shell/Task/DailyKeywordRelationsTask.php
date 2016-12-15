<?php
namespace App\Shell\Task;

use Cake\Console\Shell;

use Cake\I18n\Time;

use Cake\Datasource\ConnectionManager;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Table\ResearchesTable;
use App\Model\Entity\Research;
use App\Model\Table\SubjectsTable;
use App\Model\Entity\Subject;
use App\Model\Table\RelationsTable;
use App\Model\Entity\Relation;
use App\Model\Table\ContentsTable;
use App\Model\Entity\Content;
use App\Model\Table\EmotionsTable;
use App\Model\Entity\Emotion;
use App\Model\Table\EmotionTypesTable;
use App\Model\Entity\EmotionType;
use Cake\Controller\Component\RequestHandlerComponent;

class DailyKeywordRelationsTask extends Shell
{

	public function initialize()
    {
        parent::initialize();
        $this->loadModel('Contents');
    }


    public function main()
    {

    	$this->out("DataDumper:Daily Keyword Relations:ok");

    }

    public function dump($researches = -1){
    	$nodes = array();
		$links = array();

		$results = array();
		$resultsrel = array();


			$researcharray = explode(",", $researches  );

			//$contents = TableRegistry::get('Contents');

			$q1 = $this->Contents
			    ->find('all');
			
			if( $researches==-1 ){
			
				$q1->contain(['Entities'])
			    ->matching('Entities')
			    ->where([
			        'Entities.entity_type_id' => 1
			    ]);

			}else{
			    
			    $q1->contain(['Entities'])
			    ->matching('Entities')
			    ->where([
			        'Contents.research_id IN' => $researcharray,
			        'Entities.entity_type_id' => 1
			    ]);

			}

			$hold = array();
			
			foreach($q1 as $c){

				foreach($c->entities as $e){

					if($e->entity_type_id==1){
						if( !isset( $hold[ $e->entity ]) ){
							$hold[ $e->entity ] = array();
						}
						foreach($c->entities as $e2){
							if( $e->entity_type_id==1 && $e->entity!=$e2->entity){
								if(!isset($hold[$e->entity][$e2->entity])){
									$hold[$e->entity][$e2->entity] = 1;
								} else {
									$hold[$e->entity][$e2->entity] = $hold[$e->entity][$e2->entity] + 1;
								}
							}
						}						
					}
				}
			}
			//foreach
			
		
		$result = array();
		$result["nodes"] = $nodes;
		//$result["links"] = $links;

		$result["links"] = array();

		foreach ($hold as $key => $arr) {

			foreach ($arr as $key2 => $value2) {
				$o = new \stdClass();
				$o->source = $key;
				$o->target = $key2;
				$o->weight = $value2;
				$result["links"][] = $o;
			}
			
		}

		

		$jsoncontent = json_encode( $result );
		$now = Time::now();
		$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-DailyKeywordRelations.json";
		$this->createFile($filename, $jsoncontent);

    }
}
?>