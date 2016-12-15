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

class DailyKeywordsTask extends Shell
{

	public function initialize()
    {
        parent::initialize();
        $this->loadModel('ContentsEntities');
    }


    public function main()
    {

    	$this->out("DataDumper:Daily Keywords:ok");

    }

    public function dump($researches = -1){
    	$results = array();


			$researcharray = explode(",", $researches  );

			//$ces = TableRegistry::get('ContentsEntities');

			$q1 = $this->ContentsEntities
			    ->find('all');

			if( $researches==-1 ){
				$q1
			    ->contain(['Entities'])
			    ->matching('Entities')
			    ->where([
			        'Entities.entity_type_id' => 1
			    ]);
			} else {
			    $q1
			    ->contain(['Entities'])
			    ->matching('Entities')
			    ->where([
			        'ContentsEntities.research_id IN' => $researcharray,
			        'Entities.entity_type_id' => 1
			    ]);
			}

			foreach($q1 as $ce){
				$label = $ce->entity->entity;
				if(isset($results[$label])){
					$results[$label] = $results[$label] +1;
				} else {
					$results[$label] = 1;
				}
			}

		$children = array();
		foreach ($results as $k => $r) {
			$c = new \stdClass();
			$c->name = $k;
			$c->value = $r;
			$children[] = $c;
		}

		$jsoncontent = json_encode( $children );
		$now = Time::now();
		$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-DailyKeywords.json";
		$this->createFile($filename, $jsoncontent);
    }
}
?>