<?php
namespace App\Controller;
set_time_limit(0);

use Cake\Datasource\ConnectionManager;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Table\VisualisationsTable;
use App\Model\Table\ResearchesTable;
use App\Model\Entity\Research;

class HomeController extends AppController
{
	public function beforeFilter(Event $event){
		parent::beforeFilter($event);
		/*
		$this->Auth->allow( [ 'controller' => 'home' , 'action' => 'home' ] );
		$this->Auth->allow( [ 'controller' => 'home' , 'action' => 'visualize' ] );
		$this->Auth->allow( [ 'controller' => 'home' , 'action' => 'getviz' ] );
		*/

		$this->Auth->allow( [ 'home' , 'visualize', 'getviz' ] );
	}


	public function home(  ){

		$authed = false;
		if(!is_null($this->Auth->user('id'))){
			$authed = true;
		}
		$this->set("authed",$authed);
	}


	public function visualize(  ){
		$vt = new VisualisationsTable();
		$rt = $vt->getVisualizations();


		$researches = TableRegistry::get('Researches');
		$query = $researches->find('all');

		$resar = array();
		foreach($query as $r){
			$o = new \stdClass();
			$o->id = $r->id;
			$o->name = $r->name;
			$resar[] = $o;
		}


		$this->set('visualizations' , $rt);
		$this->set('researchlist' , $resar);
	}


	public function manage(  ){
		


		$toHandle = $this->request->query('which');
		$toHandleArray = explode(",",$toHandle);
		$action = $this->request->query('action');
		if($action!=""){
			if($action=="export"){
				// export research
				$result = "";
				$csvfile = "";
				foreach ($toHandleArray as $res) {
					$result = $result . $this->export($res , $csvfile );
				}
				$this->set('result' , $result);
				$this->set('csvfile' , $csvfile);

				header("Content-type: application/octet-stream");
      			header("Content-Disposition: attachment; filename=\"export-research-" . str_replace(",", "-", $toHandle) . ".csv\"");
      			echo( $csvfile );

			} else if($action=="delete"){
				// delete research
				$result = "";
				foreach ($toHandleArray as $res) {
					$result = $result . $this->delete($res);
				}
				$this->set('result' , $result);
			}
		}


		$researches = TableRegistry::get('Researches');
		$query = $researches->find('all');

		$resar = array();
		foreach($query as $r){
			$o = new \stdClass();
			$o->id = $r->id;
			$o->name = $r->name;
			$resar[] = $o;
		}


		$this->set('researchlist' , $resar);
	}

	public function export( $res , &$csvfile ){
		$result = "";

		$csvfile = $csvfile . "\n\n-------------------------------------\n";
		$csvfile = $csvfile . "Exporting research id: " . $res;
		$csvfile = $csvfile . "\n";


		$connection = ConnectionManager::get('default');

		$querystring = 'SELECT name FROM researches WHERE id=' . $res;
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . "Research name: " . $r["name"];
				$csvfile = $csvfile . "\n";				
			}
		}

		$querystring = 'SELECT content,lat,lng,language FROM research_elements WHERE research_id=' . $res;
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . "Research element: " . $r["content"] . ",(" . $r["lat"] . "," . $r["lng"] . ")," . $r["language"];
				$csvfile = $csvfile . "\n";				
			}
		}

		$csvfile = $csvfile . "\n\n";
		$csvfile = $csvfile . "CONTENT";
		$csvfile = $csvfile . "\nid,subject_id,link,content,created_at,language,favorite_count,share_count,lat,lng,comfort,energy\n";

		$querystring = 'SELECT id,subject_id,link,content,created_at,language,favorite_count,retweet_count,lat,lng,comfort,energy FROM contents WHERE research_id=' . $res;
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . $r["id"] . "," . $r["subject_id"] . "," . $r["link"] . "," . str_replace(",", " ", $r["content"] ) . "," . $r["created_at"] . "," . $r["language"] . "," . $r["favorite_count"] . "," . $r["retweet_count"] . ",(" . $r["lat"] . "," . $r["lng"] . ")," . $r["comfort"] . "," . $r["energy"];
				$csvfile = $csvfile . "\n";				
			}
		}


		$csvfile = $csvfile . "\n\n";
		$csvfile = $csvfile . "ENTITIES";
		$csvfile = $csvfile . "\ncontent id,entity type,entity\n";

		$querystring = 'SELECT ce.content_id as content_id , e.entity_type_id as entity_type, e.entity as entity FROM contents_entities ce, entities e WHERE ce.research_id=' . $res . ' AND ce.entity_id=e.id';
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . $r["content_id"] . "," . $r["entity_type"] . "," . $r["entity"];
				$csvfile = $csvfile . "\n";				
			}
		}



		$csvfile = $csvfile . "\n\n";
		$csvfile = $csvfile . "EMOTIONS";
		$csvfile = $csvfile . "\ncontent id,emotion\n";

		$querystring = 'SELECT e.content_id as content_id, et.label as emotion FROM emotions e, emotion_types et WHERE e.research_id=' . $res . ' AND e.emotion_type_id=et.id';
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . $r["content_id"] . "," . $r["emotion"];
				$csvfile = $csvfile . "\n";				
			}
		}


		$csvfile = $csvfile . "\n\n";
		$csvfile = $csvfile . "RELATIONS";
		$csvfile = $csvfile . "\nsubject 1 id, subject 2 id,weight\n";

		$querystring = 'SELECT subject_1_id, subject_2_id, c FROM relations r WHERE r.research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . $r["subject_1_id"] . "," . $r["subject_2_id"] . "," . $r["c"];
				$csvfile = $csvfile . "\n";				
			}
		}


		$csvfile = $csvfile . "\n\n";
		$csvfile = $csvfile . "SUBJECTS";
		$csvfile = $csvfile . "\n\n";

		$querystring = 'SELECT  id,location, followers_count, friends_count, listed_count, language,profile_url,profile_image_url FROM subjects s WHERE s.research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring)->fetchAll('assoc');
			foreach ($re as $r) {
				$csvfile = $csvfile . $r["id"] . "," . str_replace(",", " ", $r["location"] ). "," . $r["followers_count"] . "," . $r["friends_count"] . "," . $r["listed_count"];
				$csvfile = $csvfile . "\n";				
			}
		}

		$result = "Researches exported.";

		return $result;
	}

	public function delete( $res ){
		$result = "";

		$connection = ConnectionManager::get('default');

		
		
		$querystring = 'DELETE FROM emotions WHERE research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}

		
		$querystring = 'DELETE FROM relations WHERE research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}

		$querystring = 'DELETE FROM contents_entities WHERE research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}
		
		$querystring = 'DELETE FROM contents WHERE research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}

		

		$querystring = 'DELETE FROM subjects WHERE research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}


		$querystring = 'DELETE FROM research_elements WHERE research_id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}


		$querystring = 'DELETE FROM researches WHERE id=' . $res ;
		if($querystring!=""){
			$re = $connection->execute($querystring);
		}

		$result = "Researches " . $res . " deleted. | ";

		return $result;	
	}

	public function getviz(){
		$vt = new VisualisationsTable();

		$viz = $this->request->query('which');

		$CSSFiles = $vt->getCSSFilesList($viz);
		$JSFiles = $vt->getJSFilesList($viz);

		$researcesstring = $this->request->query('researches');
		$researchesids = explode(",", $researcesstring);

		$this->set("viz", $viz);
		$this->set("researches", $researchesids);
		$this->set("jsfiles", $JSFiles);
		$this->set("cssfiles", $CSSFiles);

	}

}