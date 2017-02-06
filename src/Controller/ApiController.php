<?php
namespace App\Controller;


require_once ('../vendor/emotions/stopwords.php');

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

class ApiController extends AppController
{

	public function beforeFilter(Event $event){
		parent::beforeFilter($event);
		$this->Auth->allow( ['getRelations','getWordNetwork' , 'getEmotions', 'getTimeline', 'getEmotionsTimeline', 'getWordCloud' , 'getEnergyComfortDistribution', 'getGeoPoints', 'getGeoEmotionPoints','getHashtagNetwork', 'getHashtagCloud', 'getSentiment','getContentMatch','getImages','getNumberOfSubjects','getRecent','getContentByComfortEnergy','getMaxMinComfortEnergyPerResearch','getImagesByComfortEnergy','getMultipleKeywordsTimeline','getDesireTimeline','getStatistics','getSentimentSeries','getEmotionsSeries','getActivity','getTopUsers','getKeywordSeries',"getEmotionalBoundariesSeries","getMultipleMentionsSeries","getEmotionallyWeightedKeywordSeries" ] );
		
		$this->response->header('Access-Control-Allow-Origin','*');
        $this->response->header('Access-Control-Allow-Methods','*');
        $this->response->header('Access-Control-Allow-Headers','X-Requested-With');
        $this->response->header('Access-Control-Allow-Headers','Content-Type, x-xsrf-token');
        $this->response->header('Access-Control-Max-Age','172800');

		$this->RequestHandler->renderAs($this, 'json');
	    $this->response->type('application/json');
	    $this->set('_serialize', true);
	}

	public function initialize()
	{
		parent::initialize();
	    $this->loadComponent('RequestHandler');
	}

	public function getStatistics(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('mode'))  && $this->request->query('mode')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			$mode = $this->request->query('mode');

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$interval = "1 DAY";
			if($mode=="week"){
				$interval = "1 WEEK";
			} else if($mode=="month"){
				$interval = "1 MONTH";
			} else if($mode=="all"){
				$interval = "1 YEAR";
			}
			
			$ncontents = 0;
			$nusers = 0;

			$querystring = 'SELECT count(*) as c FROM ( SELECT  DISTINCT subject_id as s FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ) a';

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$nusers = $v["c"];
					$results["nusers"] = $nusers;
				}	
			}

			$querystring = 'SELECT  count(*) as c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$ncontents = $v["c"];
					$results["ncontents"] = $ncontents;
				}	
			}
			

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}

	public function getRelations(){
		$maxweight = 1;

		$nodes = array();
		$links = array();

		$results = array();
		$resultsrel = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT s1.id as sourceid, s1.screen_name as sourcenick , s1.profile_url as sourceurl, s2.id as targetid, s2.screen_name as targetnick , s2.profile_url as targeturl FROM subjects s1, subjects s2, relations r WHERE r.research_id IN (' .  $this->request->query('researches') .  ') AND  s1.id=r.subject_1_id AND s2.id=r.subject_2_id ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}

				$querystring = $querystring . ' AND  ( ( r.subject_1_id IN ( SELECT subject_id as id FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') )  )    OR    ( r.subject_1_id IN ( SELECT subject_id as id FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ) )  )  ';
			}

			$querystring = $querystring . ' ORDER BY r.id DESC ';

			if( null!==$this->request->query('limit')){
				// ripristinare?
				//$querystring = $querystring . ' LIMIT ' . $this->request->query('limit');
			}

			
			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				$contentres = array();

				$urls = array();
				$rels = array();
				$weights = array();

				foreach ($re as $c) {
				
					

					if(!isset($urls[$c["sourcenick"]])){
						$urls[$c["sourcenick"]] = $c["sourceurl"];
					}

					if(!isset($urls[$c["targetnick"]])){
						$urls[$c["targetnick"]] = $c["targeturl"];
					}

					if(!isset(  $rels[$c["sourcenick"]]  )){
						$rels[$c["sourcenick"]] = array();  
						$rels[$c["sourcenick"]][] = $c["targetnick"];
						$weights[ 
							$c["sourcenick"]
							. "." 
							. $c["targetnick"] 
						] = 1;
					} else {
						if(!in_array($c["targetnick"], $rels[$c["sourcenick"]] )){
							$rels[$c["sourcenick"]][] =  $c["targetnick"] ;
							if(  isset(  $weights[ $c["sourcenick"] . "." . $c["targetnick"] ]  ) ){
								$weights[ $c["sourcenick"] . "." . $c["targetnick"] ] = $weights[ $c["sourcenick"] . "." . $c["targetnick"] ] + 1;
								if(  $weights[ $c["sourcenick"] . "." . $c["targetnick"] ] > $maxweight){
									$maxweight = $weights[ $c["sourcenick"] . "." . $c["targetnick"] ];
								}
							} else {
								$weights[ $c["sourcenick"] . "." . $c["targetnick"] ] = 1;
							}
						}
					}
				}// foreach

				foreach ($urls as $n1 => $u1) {

					$add = true;

					$o = new \stdClass();
					$o->id = $n1;
					$o->nick = $n1;
					$o->pu = $u1;

					$w=1;
					if(isset($rels[$n1])){
						$w = count($rels[$n1]);

						foreach ($rels[$n1] as $n2) {
							$o2 = new \stdClass();
							$o2->source = $n1;
							$o2->target = $n2;
							$w2 = 1;
							if( isset( $weights[$n1 . "." . $n2]  )){
								$w2 = $weights[$n1 . "." . $n2];
							}

							if( null!==$this->request->query('limit')){
								if( $weights[$n1 . "." . $n2]<$maxweight/2 ){
									$add = false;
								}
							}

							$o2->weight = $w2;
							if($add){ $links[] = $o2; }
						}

					}

					$o->weight = $w;

					if($add){ $nodes[] = $o; }

				}


			} //if query empty


			
		}


		$this->set(compact('nodes', 'links'));
		$this->set('_serialize', ['nodes', 'links']);

	}



	public function getMaxMinComfortEnergyPerResearch(){
		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$comforts = array();
			$energies = array();

			
			// return all the latest ones
			$querystring = 'SELECT MAX(comfort) as maxcomfort, MIN(comfort) as mincomfort, MAX(energy) as maxenergy, MIN(energy) as minenergy FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ')';

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$o = new \stdClass();
					$o->maxcomfort = $v["maxcomfort"];
					$o->mincomfort = $v["mincomfort"];
					$o->maxenergy = $v["maxenergy"];
					$o->minenergy = $v["minenergy"];
					$results[] = $o;
				}	
			}
			

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}




	public function getContentByComfortEnergy(){
		$results = array();

		$delta = 50;
		if(!is_null($this->request->query('delta'))  && $this->request->query('delta')!="" ){
			$delta = $this->request->query('delta');
		}

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$comforts = array();
			$energies = array();

			if(!is_null($this->request->query('comfort'))  && $this->request->query('comfort')!="" ){
				$comforts = explode(",", $this->request->query('comfort'));
			}

			if(!is_null($this->request->query('energy'))  && $this->request->query('energy')!="" ){
				$energies = explode(",", $this->request->query('energy'));
			}

			$querystring = "";

			if( count($comforts)>0 && count($energies)>0 && count($comforts)==count($energies)  ){
				$querystring = 'SELECT c.id as id,c.content as content,c.comfort as comfort,c.energy as energy FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND (';

				for($k = 0 ; $k<count($comforts);$k++){
					$querystring = $querystring . 
									" ( c.comfort>" . ($comforts[$k]-$delta) . 
									" AND " .
									" c.comfort<" . ($comforts[$k]+$delta) . 
									" AND c.energy>" . ($energies[$k]-$delta) . 
									" AND " .
									" c.energy<" . ($energies[$k]+$delta) . " ) ";
					if($k<(count($comforts)-1) ){
						$querystring = $querystring . " OR ";
					}
				}

				$querystring = $querystring . " ) LIMIT 0,100 ";

			} else if ( count($comforts)==0  && count($energies)==0 ){

				// return all the latest ones
				$querystring = 'SELECT c.id as id,c.content as content,c.comfort as comfort,c.energy as energy FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') LIMIT 0,100';

			}

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$o = new \stdClass();
					$o->id = $v["id"];
					$o->content = $v["content"];
					$o->comfort = $v["comfort"];
					$o->energy = $v["energy"];
					$results[] = $o;
				}	
			}
			

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}






	public function getImagesByComfortEnergy(){
		$results = array();

		$delta = 50;
		if(!is_null($this->request->query('delta'))  && $this->request->query('delta')!="" ){
			$delta = $this->request->query('delta');
		}

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$comforts = array();
			$energies = array();

			if(!is_null($this->request->query('comfort'))  && $this->request->query('comfort')!="" ){
				$comforts = explode(",", $this->request->query('comfort'));
			}

			if(!is_null($this->request->query('energy'))  && $this->request->query('energy')!="" ){
				$energies = explode(",", $this->request->query('energy'));
			}

			$querystring = "";

			if( count($comforts)>0 && count($energies)>0 && count($comforts)==count($energies)  ){
				$querystring = 'SELECT e.entity as entity ,c.comfort as comfort,c.energy as energy FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND (e.entity LIKE "%jpg" OR e.entity LIKE "%png" ) AND (';

				for($k = 0 ; $k<count($comforts);$k++){
					$querystring = $querystring . 
									" ( c.comfort>" . ($comforts[$k]-$delta) . 
									" AND " .
									" c.comfort<" . ($comforts[$k]+$delta) . 
									" AND c.energy>" . ($energies[$k]-$delta) . 
									" AND " .
									" c.energy<" . ($energies[$k]+$delta) . " ) ";
					if($k<(count($comforts)-1) ){
						$querystring = $querystring . " OR ";
					}
				}

				$querystring = $querystring . " ) ORDER BY c.created_at DESC LIMIT 0,100 ";

			} else if ( count($comforts)==0  && count($energies)==0 ){

				// return all the latest ones
				$querystring = 'SELECT e.entity as entity ,c.comfort as comfort,c.energy as energy  FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND (e.entity LIKE "%jpg" OR e.entity LIKE "%png" ) ORDER BY c.created_at DESC LIMIT 0,100';

			}

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$o = new \stdClass();
					$o->entity = $v["entity"];
					$o->comfort = $v["comfort"];
					$o->energy = $v["energy"];
					$results[] = $o;
				}	
			}
			

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}






	public function getNumberOfSubjects(){
		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$re = $connection->execute('SELECT count(*) as c FROM subjects s WHERE s.research_id IN (' .  $this->request->query('researches') .  ')')->fetchAll('assoc');
			
			foreach ($re as $v) {
				$o = new \stdClass();
				$o->c = $v["c"];
				$results[] = $o;
			}
			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}


	public function getRecent(){
		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$re = $connection->execute('SELECT count(*) as c FROM contents s WHERE s.research_id IN (' .  $this->request->query('researches') .  ') AND created_at > DATE_SUB(CURDATE(), INTERVAL 10 MINUTE)')->fetchAll('assoc');
			
			foreach ($re as $v) {
				$o = new \stdClass();
				$o->c = $v["c"];
				$results[] = $o;
			}
			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}


	public function getImages(){
		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			if( null!==$this->request->query('limit')){

				$re = $connection->execute('SELECT e.id as id, e.entity as entity FROM contents_entities ce, entities e WHERE ce.research_id IN (' .  $this->request->query('researches') .  ') AND e.id=ce.entity_id AND ( e.entity LIKE "%jpg" OR e.entity LIKE "%png" ) ORDER BY ce.id DESC LIMIT 0,' . $this->request->query('limit'))->fetchAll('assoc');
			} else {
				$re = $connection->execute('SELECT e.id as id, e.entity as entity FROM contents_entities ce, entities e WHERE ce.research_id IN (' .  $this->request->query('researches') .  ') AND e.id=ce.entity_id AND ( e.entity LIKE "%jpg" OR e.entity LIKE "%png" )')->fetchAll('assoc');
			}

			foreach ($re as $v) {
				$o = new \stdClass();
				$o->id = $v["id"];
				$o->entity = $v["entity"];
				$results[] = $o;
			}
			// use connectionmanager end

			// calc relations

			// do nodes
			/*
			use QueryBuilder
			$ce = TableRegistry::get('ContentsEntities');

			$q1 = $ce->find('all')->contains("Entities");

			if( null!==$this->request->query('limit')){
				$q1->where( [
						'research_id IN' => $researcharray ,
						"OR" => [
							['Entities.entity LIKE' => "%png"],
							['Entities.entity LIKE' => "%jpg"]
						]
						
					] )
	    			->select([
	    				"id" => 'Entities.id', 
	    				"entity" => 'Entities.entity'
	    			])
	    			->order(['id' => 'DESC'])
	    			->limit(  $this->request->query('limit')  );
			} else {
					$q1->where( [
						'research_id IN' => $researcharray ,
						"OR" => [
							['Entities.entity LIKE' => "%png"],
							['Entities.entity LIKE' => "%jpg"]
						]
						
					] )
	    			->select([
	    				"id" => 'Entities.id', 
	    				"entity" => 'Entities.entity'
	    			])
	    			->order(['id' => 'DESC']);
			}

				

	    	foreach($q1 as $s){
	    		$o = new \stdClass();
	    		$o->id = $s->id;
	    		$o->entity = $s->entity;
	    		$results[] = $o;
	    	}
			// do nodes end
		
		*/
		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}

	function getWordNetwork(){

		$stopwords = new \StopWords();

		$nodes = array();
		$links = array();

		$results = array();
		$resultsrel = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$contents = TableRegistry::get('Contents');

			$q1 = null;

			$conditions = array();
			$conditions['research_id IN'] = $researcharray;
			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){
				$conditions['language'] = $this->request->query('language');
			}

			if( null!==$this->request->query('limit')){
				$q1 = $contents->find('all')
					->where( $conditions )
	    			->select(['content','energy','comfort'])
	    			->order(['id' => 'DESC'])
	    			->limit(  $this->request->query('limit')  );
			} else {
				$q1 = $contents->find('all')
					->where( $conditions )
	    			->select(['content','energy','comfort']);
			}

			$renergy = array();
			$rcomfort = array();


			$idid = 1;

			foreach($q1 as $c){

				$val = $c->content;

				$val = preg_replace('/#\S+ */', '', $val);

				$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
				$val = preg_replace($regex, ' ', $val);
				$val = preg_replace("/[^[:alnum:][:space:]]/ui", ' ', $val);

				$val = strtoupper($val);

				$val = str_replace("HTTPS", ' ', $val); // remove https
				$val = str_replace("HTTP", ' ', $val); // remove http

				$val = str_replace("\t", ' ', $val); // remove tabs
				$val = str_replace("\n", ' ', $val); // remove new lines
				$val = str_replace("\r", ' ', $val); // remove carriage returns

				$val = strtolower($val);
				$val = preg_replace("#[[:punct:]]#", " ", $val);
				$val = preg_replace("/[^A-Za-z]/", ' ', $val);

				for($i = 0; $i<count($stopwords->stopwords); $i++){
					$val = preg_replace('/\b' . $stopwords->stopwords[$i] . '\b/u', ' ', $val);
				}

				$words = explode(" ", $val);

				$resultcontent = array();

				for($i=0; $i<count($words); $i++){

					if(trim($words[$i])!="" && strlen($words[$i])>3 ){
						if(isset($results[$words[$i]])){
							$results[$words[$i]] = $results[$words[$i]] + 1;
						} else {
							$results[$words[$i]] = 1;
						}
						if(isset($resultcontent[$words[$i]])){
							$resultcontent[$words[$i]] = $resultcontent[$words[$i]] + 1;
						} else {
							$resultcontent[$words[$i]] = 1;
						}
						if(!isset($renergy[$words[$i]])){
							$renergy[$words[$i]] = 0;
						}
						if($c->energy!=0){ $renergy[$words[$i]] = ($renergy[$words[$i]]+$c->energy)/2; }
						if(!isset($rcomfort[$words[$i]])){
							$rcomfort[$words[$i]] = 0;
						}
						if($c->comfort!=0){ $rcomfort[$words[$i]] = ($rcomfort[$words[$i]]+$c->comfort)/2; }
					}
				}

				$ii = 0;
				foreach ($resultcontent as $key1 => $value1) {
					$jj = 0;
					foreach ($resultcontent as $key2 => $value2) {
						if($key1!=$key2){
							$oo = new \stdClass();
							$oo->id = $idid;
							$oo->source = $key1;
							$oo->target = $key2;
							$oo->sourceid = $ii;
							$oo->targetid = $jj;
							$oo->weight = $value1 + $value2;
							$links[] = $oo;
							$idid++;
						}
						$jj++;
					}
					$ii++;			
				}

			}
			//foreach

			foreach ($results as $key => $value) {
				$o = new \stdClass();
				$o->id = $key;
				$o->word = $key;
				$o->weight = $value;
				$o->energy = $renergy[$key];
				$o->comfort = $rcomfort[$key];
				$nodes[] = $o;
			}

			for($i = 0; $i<count($links); $i++){
				$found1 = false;
				$found2 = false;
				for($j=0; $j<count($nodes)&&!$found1&&!$found2; $j++){
					if($nodes[$j]->word==$links[$i]->source){
						$links[$i]->sourceid = $j;
						$found1 = true;
					}
					if($nodes[$j]->word==$links[$i]->target){
						$links[$i]->targetid = $j;
						$found2 = true;
					}
				}
			}

		}

		$this->set(compact('nodes', 'links'));
		$this->set('_serialize', ['nodes', 'links']);

	}




	function getEmotions(){

		$results = array();
		$et = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$emotions = TableRegistry::get('Emotions');
			$emotiontypes = TableRegistry::get('EmotionTypes');

			$q0 = $emotiontypes->find('all');
			foreach($q0 as $e){
				$o = new \stdClass();
				$o->label = $e->label;
				$o->id = $e->id;
				$o->energy = $e->energy;
				$o->comfort = $e->comfort;
				$et[] = $o;
			}

			$q1 = null;

			$conditions = array();
			$conditions['research_id IN'] = $researcharray;

			$q1 = $emotions->find('all');

			$q1->select([
				    'value' => $q1->func()->count('emotion_type_id'),
				    'emotion_id' => 'emotion_type_id'
				])
				->where( $conditions )
				->group('emotion_id');

			foreach($q1 as $c){
				$found = false;
				for($i=0; $i<count($et) && !$found;$i++){
					if($et[$i]->id==$c->emotion_id){
						$found = true;
						$o = new \stdClass();
						$o->label = $et[$i]->label;
						$o->value = $c->value;
						$results[] = $o;
					}
				}
			}
			//foreach

		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}




	function getTimeline(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$contents = TableRegistry::get('Contents');

			$q1 = null;

			$conditions = array();
			$conditions['research_id IN'] = $researcharray;

			$q1 = $contents->find('all');

			$q1->select([
				    'd' => 'DAY(created_at)',//$q1->func()->count('emotion_type_id'),
				    'm' => 'MONTH(created_at)',//$q1->func()->count('emotion_type_id'),
				    'y' => 'YEAR(created_at)',//$q1->func()->count('emotion_type_id'),
				    'value' => $q1->func()->count('id')
				])
				->where( $conditions )
				->group(
					[
						'YEAR(created_at)', 'MONTH(created_at)', 'DAY(created_at)'
					]
				)
				->order(['created_at' => 'DESC']);

			foreach($q1 as $c){
				$o = new \stdClass();
				$o->date =  ($c->d<10?"0":"") . $c->d . "-" . ($c->m<10?"0":"") . $c->m . "-" . ($c->y<100?"19":"") . $c->y ;
				$o->close = $c->value;
				$results[] = $o;
			}
			//foreach

		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getEmotionsTimeline(){

		$results = array();
		$et = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$emotions_types = TableRegistry::get('EmotionTypes');
			$q0 = $emotions_types->find('all');
			foreach($q0 as $e){
				$o = new \stdClass();
				$o->label = $e->label;
				$o->id = $e->id;
				$o->energy = $e->energy;
				$o->comfort = $e->comfort;
				$et[] = $o;
			}

			$researcharray = explode(",", $this->request->query('researches')  );

			$emotions = TableRegistry::get('Emotions');

			$q1 = null;

			$conditions = array();
			$conditions['Emotions.research_id IN'] = $researcharray;

			$q1 = $emotions->find('all')->contain(['Contents']);

			$q1->select([
					'emotion_type_id' => 'Emotions.emotion_type_id',
				    'd' => 'DAY(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
				    'm' => 'MONTH(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
				    'y' => 'YEAR(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
				    'value' => $q1->func()->count('Emotions.id')
				])
				->where( $conditions )
				->group(
					[
						'YEAR(Contents.created_at)', 'MONTH(Contents.created_at)', 'DAY(Contents.created_at)', 'Emotions.emotion_type_id'
					]
				)
				->order(['Contents.created_at' => 'DESC']);

			foreach($q1 as $c){
				$o = new \stdClass();
				$o->emotion_type_id = $c->emotion_type_id;

				$o->emotion_label = "";

				$found = false;
				for($i=0; $i<count($et) && !$found; $i++){
					if($o->emotion_type_id==$et[$i]->id){
						$found = true;
						$o->emotion_label = $et[$i]->label;
					}
				}

				$o->date =  ($c->d<10?"0":"") . $c->d . "-" . ($c->m<10?"0":"") . $c->m . "-" . ($c->y<100?"19":"") . $c->y ;
				$o->close = $c->value;
				$results[] = $o;
			}
			//foreach

		}

		$restot = array();
		foreach ($results as $o) {
			$lab = $o->emotion_label;
			unset($o->emotion_type_id);
			unset($o->emotion_label);
			$restot[$lab][] = $o;
		}

		$results = $restot;

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getMultipleKeywordsTimeline(){

		$results = array();
		$et = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('keywords'))  && $this->request->query('keywords')!="" ){

			$et = explode(",", $this->request->query('keywords') );

			$researcharray = explode(",", $this->request->query('researches')  );

			$contents = TableRegistry::get('Contents');

			for($k = 0; $k<count($et) ; $k++){

				$et[$k] = strtoupper($et[$k]);

				$q1 = null;

				$conditions = array();
				$conditions['Contents.research_id IN'] = $researcharray;
				$conditions['UCASE(Contents.content) LIKE'] =  '%' . $et[$k] . '%';

				$q1 = $contents->find('all');

				$q1->select([
						'd' => 'DAY(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
					    'm' => 'MONTH(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
					    'y' => 'YEAR(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
					    'value' => $q1->func()->count('Contents.id')
					])
					->where( $conditions )
					->group(
						[
							'YEAR(Contents.created_at)', 'MONTH(Contents.created_at)', 'DAY(Contents.created_at)'
						]
					)
					->order(['Contents.created_at' => 'DESC']);

				foreach($q1 as $c){
					$o = new \stdClass();
					$o->class = $et[$k];

					$o->class_label = $et[$k];

					$o->date =  ($c->d<10?"0":"") . $c->d . "-" . ($c->m<10?"0":"") . $c->m . "-" . ($c->y<100?"19":"") . $c->y ;
					$o->close = $c->value;
					$results[] = $o;
				}
				//foreach

			}

		}

		$restot = array();
		foreach ($results as $o) {
			$lab = $o->class_label;
			unset($o->class);
			unset($o->class_label);
			$restot[$lab][] = $o;
		}

		$results = $restot;

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getDesireTimeline(){

		$results = array();
		$et = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$contents = TableRegistry::get('Contents');


				$q1 = null;

				$conditions = array();
				$conditions['Contents.research_id IN'] = $researcharray;
				$conditions['Contents.comfort >'] =  '100';
				$conditions['Contents.energy >'] =  '100';

				$q1 = $contents->find('all');

				$q1->select([
						'd' => 'DAY(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
					    'm' => 'MONTH(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
					    'y' => 'YEAR(Contents.created_at)',//$q1->func()->count('emotion_type_id'),
					    'value' => $q1->func()->count('Contents.id')
					])
					->where( $conditions )
					->group(
						[
							'YEAR(Contents.created_at)', 'MONTH(Contents.created_at)', 'DAY(Contents.created_at)'
						]
					)
					->order(['Contents.created_at' => 'DESC']);

				foreach($q1 as $c){
					$o = new \stdClass();
					$o->class = "Desiderio";

					$o->class_label = "Desiderio";

					$o->date =  ($c->d<10?"0":"") . $c->d . "-" . ($c->m<10?"0":"") . $c->m . "-" . ($c->y<100?"19":"") . $c->y ;
					$o->close = $c->value;
					$results[] = $o;
				}
				//foreach

		}

		$restot = array();
		foreach ($results as $o) {
			$lab = $o->class_label;
			unset($o->class);
			unset($o->class_label);
			$restot[$lab][] = $o;
		}

		$results = $restot;

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getWordCloud(){

		$stopwords = new \StopWords();

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$contents = TableRegistry::get('Contents');

			$q1 = null;

			$conditions = array();
			$conditions['research_id IN'] = $researcharray;
			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){
				$conditions['language'] = $this->request->query('language');
			}

			if( null!==$this->request->query('limit')){
				$q1 = $contents->find('all')
					->where( $conditions )
	    			->select(['content'])
	    			->order(['id' => 'DESC'])
	    			->limit(  $this->request->query('limit')  );
			} else {
				$q1 = $contents->find('all')
					->where( $conditions )
	    			->select(['content']);
			}

			foreach($q1 as $c){

				$val = $c->content;

				$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
				$val = preg_replace($regex, ' ', $val);
				$val = preg_replace("/[^[:alnum:][:space:]]/ui", ' ', $val);

				$val = strtoupper($val);

				$val = str_replace("HTTPS", ' ', $val); // remove https
				$val = str_replace("HTTP", ' ', $val); // remove http

				$val = str_replace("\t", ' ', $val); // remove tabs
				$val = str_replace("\n", ' ', $val); // remove new lines
				$val = str_replace("\r", ' ', $val); // remove carriage returns

				$val = strtolower($val);
				$val = preg_replace("#[[:punct:]]#", " ", $val);
				$val = preg_replace("/[^A-Za-z]/", ' ', $val);

				for($i = 0; $i<count($stopwords->stopwords); $i++){
					$val = preg_replace('/\b' . $stopwords->stopwords[$i] . '\b/u', ' ', $val);
				}

				$words = explode(" ", $val);

				$resultcontent = array();

				for($i=0; $i<count($words); $i++){

					if(trim($words[$i])!="" && strlen($words[$i])>3 ){
						if(isset($results[$words[$i]])){
							$results[$words[$i]] = $results[$words[$i]] + 1;
						} else {
							$results[$words[$i]] = 1;
						}
						if(isset($resultcontent[$words[$i]])){
							$resultcontent[$words[$i]] = $resultcontent[$words[$i]] + 1;
						} else {
							$resultcontent[$words[$i]] = 1;
						}
					}
				}


			}
			//foreach

		}


		$children = array();
		foreach ($results as $k => $r) {
			$c = new \stdClass();
			$c->name = $k;
			$c->value = $r;
			$children[] = $c;
		}

		$this->set(compact('children'));
		$this->set('_serialize', ['children']);

	}





	function getEnergyComfortDistribution(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$contents = TableRegistry::get('Contents');

			$q1 = null;

			$conditions = array();
			$conditions['research_id IN'] = $researcharray;
			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){
				$conditions['language'] = $this->request->query('language');
			}

			if( null!==$this->request->query('limit')){
				$q1 = $contents->find('all');

	    		$q1->select([
	    				'comfort' => 'comfort', 
	    				'energy' => 'energy',
	    				'c' => $q1->func()->count('id'),
	    			])
	    			->where( $conditions )
	    			->order(['id' => 'DESC'])
	    			->group(
						[
							'comfort', 'energy'
						]
					)
	    			->limit(  $this->request->query('limit')  );
			} else {
				$q1 = $contents->find('all');
	    		$q1->select([
	    				'comfort' => 'comfort', 
	    				'energy' => 'energy',
	    				'c' => $q1->func()->count('id')
	    			])
	    			->where( $conditions )
	    			->group(
						[
							'comfort', 'energy'
						]
					)
	    			;
			}

			foreach($q1 as $c){

				if($c->comfort!=0 || $c->energy!=0){

					$o = new \stdClass();
					$o->c = $c->c;
					$o->comfort = $c->comfort;
					$o->energy = $c->energy;

					$results[] = $o;	

				}
				
			}
			//foreach

		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getGeoPoints(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){


			$connection = ConnectionManager::get('default');

			$researcharray = explode(",", $this->request->query('researches')  );


			$querystring = 'SELECT  lat,lng,count(*) as c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){

				$querystring = $querystring . " AND language='" . $this->request->query('language') . "'";

			}

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}


			$querystring = $querystring . " GROUP BY lat,lng";

			if( null!==$this->request->query('limit')){


				$querystring = $querystring . " LIMIT 1," . $this->request->query('limit');


			}

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $c) {

					if( ($c["lat"]!=0 || $c["lng"]!=0) && ($c["lat"]!=-999 || $c["lng"]!=-999)  ){

						$o = new \stdClass();
						$o->c = floatval($c["c"]);
						$o->lat = floatval($c["lat"]);
						$o->lng = floatval($c["lng"]);

						$results[] = $o;	
						
					}

				}	
			}


		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}


	function getGeoEmotionPoints(){
		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){


			$connection = ConnectionManager::get('default');

			$researcharray = explode(",", $this->request->query('researches')  );


			$querystring = 'SELECT  et.id as emotion_id, et.label as label, lat,lng,count(*) as c FROM contents c , emotions e, emotion_types et WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND e.content_id=c.id AND e.emotion_type_id=et.id ';

			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){

				$querystring = $querystring . " AND language='" . $this->request->query('language') . "'";

			}

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}


			$querystring = $querystring . " GROUP BY lat,lng";

			if( null!==$this->request->query('limit')){


				$querystring = $querystring . " LIMIT 1," . $this->request->query('limit');


			}

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $c) {

					if( ($c["lat"]!=0 || $c["lng"]!=0) && ($c["lat"]!=-999 || $c["lng"]!=-999)  ){

						$o = new \stdClass();
						$o->c = floatval($c["c"]);
						$o->lat = floatval($c["lat"]);
						$o->lng = floatval($c["lng"]);
						$o->emotion_type_id = $c["emotion_id"];
						$o->label = $c["label"];

						$results[] = $o;	
						
					}

				}	
			}


		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}


	function getHashtagNetwork(){

		$maxweight = 0;

		$nodes = array();
		$links = array();

		$results = array();
		$resultsrel = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT c.id as cid, e.id as eid, e.entity as label FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			$querystring = $querystring . ' ORDER BY c.created_at DESC ';

			if( null!==$this->request->query('limit')){
				$querystring = $querystring . ' LIMIT ' . $this->request->query('limit');
			}
			

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				$contentres = array();

				foreach ($re as $c) {
				
					

					$o = new \stdClass();
					$o->id = $c["eid"];
					$o->label = $c["label"];
					$o->weight = 1;
					$o->cid = [ $c["cid"] ];

					// riincollare qui
					$foundnode = false;
					for($kk = 0 ; $kk<count($nodes) && !$foundnode; $kk++){
						if($nodes[$kk]->label==$o->label){
							$foundnode = true;
							$nodes[$kk]->weight = $nodes[$kk]->weight + 1;
							if($nodes[$kk]->weight>$maxweight){
								$maxweight = $nodes[$kk]->weight;
							}
							if( !in_array( $c["cid"] , $nodes[$kk]->cid ) ){
								$nodes[$kk]->cid[] = $c["cid"];
							}
						}
					}

					if(!$foundnode){
						$nodes[] = $o;
					}
					// riincollare fino a qui

				}
				//foreach

				for($i=0; $i<count($nodes);$i++){
					for($j=$i+1; $j<count($nodes);$j++){
						$intersect = array_intersect( $nodes[$i]->cid,$nodes[$j]->cid );
						if(  count(  $intersect  )!=0 ){
							$oo = new \stdClass();
							$oo->source = $nodes[$i]->label;
							$oo->target = $nodes[$j]->label;
							$oo->weight = count(  $intersect  );
							$links[] = $oo;
						}
					}
					unset($nodes[$i]->cid);			
				}


			} //if query empty
			
		}


		$this->set(compact('nodes', 'links'));
		$this->set('_serialize', ['nodes', 'links']);

	}



	function getHashtagCloud(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT  e.entity as entity FROM contents c , contents_entities ce , entities e WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 ';


			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			$querystring = $querystring . " ORDER BY ce.id DESC";
	
			if( null!==$this->request->query('limit')){

				$querystring = $querystring . " LIMIT " . $this->request->query('limit');

			}


			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$label = $v["entity"];
					if(isset($results[$label])){
						$results[$label] = $results[$label] +1;
					} else {
						$results[$label] = 1;
					}
				}	
			}

		}


		$children = array();
		foreach ($results as $k => $r) {
			$c = new \stdClass();
			$c->name = $k;
			$c->value = $r;
			$children[] = $c;
		}

		$this->set(compact('children'));
		$this->set('_serialize', ['children']);

	}


	function getSentimentSeries(){

		$results = array();

		$positive = 0;
		$negative = 0;
		$neutral = 0;

		$negative_threshold = 20;

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('sentiment'))  && $this->request->query('sentiment')!="" ){

			$sentimentcondition = " AND c.comfort > " . $negative_threshold;
			if($this->request->query('sentiment')=="negative"){
				$sentimentcondition = "AND c.comfort < " . -$negative_threshold;
			} else if($this->request->query('sentiment')=="neutral"){
				$sentimentcondition = " AND c.comfort >= " . -$negative_threshold . " AND c.comfort <= " . $negative_threshold;
			}

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}

			$querystring = $querystring . $sentimentcondition;


			if($this->request->query('mode')!="day"){
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at)';
			} else {
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at), HOUR(created_at)';
			}

			$querystring = $querystring . ' ORDER BY created_at ASC';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$y = $ce["y"];
					$m = $ce["m"];
					$d = $ce["d"];
					$h = 0;
					if($this->request->query('mode')=="day"){
						$h = $ce["h"];
					}
					$c = $ce["c"];

					$a = strptime($d . '-' . $m . '-' . $y . " " . $h . ":00", '%d-%m-%Y HH:MM');
					$timestamp = mktime($h, 0, 0, $m, $d, $y);

					//$timestamp = strtotime( $d . '-' . $m . '-' . $y . " " . $h . ":00:00");

					if( null!==$this->request->query('mode') &&  ($this->request->query('mode')!="day")  ){
						$a = strptime($d . '-' . $m . '-' . $y , '%d-%m-%Y');
						$timestamp = mktime($h, 0, 0, $m, $d, $y);
					}

					$results[] = [$timestamp,$c];

				}	
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}






	function getKeywordSeries(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('keyword'))  && $this->request->query('keyword')!="" ){

			$sentimentcondition = " AND UCASE(c.content) LIKE '%" . strtoupper($this->request->query('keyword')) . "%'";

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}

			$querystring = $querystring . $sentimentcondition;


			if($this->request->query('mode')!="day"){
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at)';
			} else {
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at), HOUR(created_at)';
			}

			$querystring = $querystring . ' ORDER BY created_at ASC';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$y = $ce["y"];
					$m = $ce["m"];
					$d = $ce["d"];
					$h = 0;
					if($this->request->query('mode')=="day"){
						$h = $ce["h"];
					}
					$c = $ce["c"];

					$a = strptime($d . '-' . $m . '-' . $y . " " . $h . ":00", '%d-%m-%Y HH:MM');
					$timestamp = mktime($h, 0, 0, $m, $d, $y);

					//$timestamp = strtotime( $d . '-' . $m . '-' . $y . " " . $h . ":00:00");

					if( null!==$this->request->query('mode') &&  ($this->request->query('mode')!="day")  ){
						$a = strptime($d . '-' . $m . '-' . $y , '%d-%m-%Y');
						$timestamp = mktime($h, 0, 0, $m, $d, $y);
					}

					$results[] = [$timestamp,$c];

				}	
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}






	function getEmotionallyWeightedKeywordSeries(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('keyword'))  && $this->request->query('keyword')!="" ){

			$sentimentcondition = " AND UCASE(c.content) LIKE '%" . strtoupper($this->request->query('keyword')) . "%'";

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*)*comfort*energy c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}

			$querystring = $querystring . $sentimentcondition;


			if($this->request->query('mode')!="day"){
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at)';
			} else {
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at), HOUR(created_at)';
			}

			$querystring = $querystring . ' ORDER BY created_at ASC';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$y = $ce["y"];
					$m = $ce["m"];
					$d = $ce["d"];
					$h = 0;
					if($this->request->query('mode')=="day"){
						$h = $ce["h"];
					}
					$c = $ce["c"];

					$a = strptime($d . '-' . $m . '-' . $y . " " . $h . ":00", '%d-%m-%Y HH:MM');
					$timestamp = mktime($h, 0, 0, $m, $d, $y);

					//$timestamp = strtotime( $d . '-' . $m . '-' . $y . " " . $h . ":00:00");

					if( null!==$this->request->query('mode') &&  ($this->request->query('mode')!="day")  ){
						$a = strptime($d . '-' . $m . '-' . $y , '%d-%m-%Y');
						$timestamp = mktime($h, 0, 0, $m, $d, $y);
					}

					$results[] = [$timestamp,$c];

				}	
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}








	function getEmotionalBoundariesSeries(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('emotion-condition'))  && $this->request->query('emotion-condition')!="" ){

			$emotioncondition = " AND " . $this->request->query('emotion-condition');

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}

			$querystring = $querystring . $emotioncondition;


			if($this->request->query('mode')!="day"){
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at)';
			} else {
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at), HOUR(created_at)';
			}

			$querystring = $querystring . ' ORDER BY created_at ASC';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$y = $ce["y"];
					$m = $ce["m"];
					$d = $ce["d"];
					$h = 0;
					if($this->request->query('mode')=="day"){
						$h = $ce["h"];
					}
					$c = $ce["c"];

					$a = strptime($d . '-' . $m . '-' . $y . " " . $h . ":00", '%d-%m-%Y HH:MM');
					$timestamp = mktime($h, 0, 0, $m, $d, $y);

					//$timestamp = strtotime( $d . '-' . $m . '-' . $y . " " . $h . ":00:00");

					if( null!==$this->request->query('mode') &&  ($this->request->query('mode')!="day")  ){
						$a = strptime($d . '-' . $m . '-' . $y , '%d-%m-%Y');
						$timestamp = mktime($h, 0, 0, $m, $d, $y);
					}

					$results[] = [$timestamp,$c];

				}	
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}









	function getMultipleMentionsSeries(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('mentions'))  && $this->request->query('mentions')!="" ){

			$mentioncondition = "";

			$ms = explode(",", $this->request->query('mentions'));
			for($i = 0; $i<count($ms); $i++){
				$ms[$i] = "UCASE(c.content) LIKE '%". strtoupper( trim( str_replace("'", "\'", $ms[$i]) )  ) . "%'";
			}

			$mentioncondition = implode(" OR ", $ms);

			$mentioncondition = " AND ( " . $mentioncondition . " )";

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*)';

			if( null!==$this->request->query('weightwith')  && $this->request->query('weightwith')!="" ){
				$querystring = $querystring . "*". $this->request->query('weightwith');
			}

			$querystring = $querystring . ' as c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}

			$querystring = $querystring . $mentioncondition;


			if($this->request->query('mode')!="day"){
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at)';
			} else {
				$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at), HOUR(created_at)';
			}

			$querystring = $querystring . ' ORDER BY created_at ASC';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$y = $ce["y"];
					$m = $ce["m"];
					$d = $ce["d"];
					$h = 0;
					if($this->request->query('mode')=="day"){
						$h = $ce["h"];
					}
					$c = $ce["c"];

					$a = strptime($d . '-' . $m . '-' . $y . " " . $h . ":00", '%d-%m-%Y HH:MM');
					$timestamp = mktime($h, 0, 0, $m, $d, $y);

					//$timestamp = strtotime( $d . '-' . $m . '-' . $y . " " . $h . ":00:00");

					if( null!==$this->request->query('mode') &&  ($this->request->query('mode')!="day")  ){
						$a = strptime($d . '-' . $m . '-' . $y , '%d-%m-%Y');
						$timestamp = mktime($h, 0, 0, $m, $d, $y);
					}

					$results[] = [$timestamp,$c];

				}	
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}






	function getActivity(){

		$data = array();


		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!=""  ){


			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT HOUR(created_at) as h, count(*) as c FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}


				$querystring = $querystring . ' GROUP BY HOUR(created_at)';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$h = $ce["h"];
					$c = $ce["c"];


					$o = new \stdClass();
					$o->x = floatval($h);
					$o->y = floatval($c);
					$o->z = floatval($c);
					$o->label = $h;

					$data[] = $o;

				}	
			}


			
		}

		$this->set(compact('data'));
		$this->set('_serialize', ['data']);


	}




	function getTopUsers(){

		$results = array();


		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!=""  ){


			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');


			$querystring = 'SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, s.followers_count as followers_count, s.listed_count as listed_count, count(*) as c , avg(c.comfort) as avgcomfort, avg(c.energy) as avgenergy, count(*)*(s.followers_count+s.listed_count) as coeff FROM contents c,subjects s WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND s.id=c.subject_id ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}


				$querystring = $querystring . ' GROUP BY c.subject_id ORDER BY coeff DESC LIMIT 50';


			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {

					$o = new \stdClass();
					$o->name = $ce["name"];
					$o->screen_name = $ce["screen_name"];
					$o->profile_url = $ce["profile_url"];
					$o->profile_image_url = $ce["profile_image_url"];
					$o->followers_count = intval( $ce["followers_count"] );
					$o->listed_count = intval(  $ce["listed_count"] );
					$o->c = intval( $ce["c"] );
					$o->coeff = intval( $ce["coeff"] );
					$o->avgcomfort = intval( $ce["avgcomfort"] );
					$o->avgenergy = intval( $ce["avgenergy"] );
					
					$results[] = $o;

				}	
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}











	function getEmotionsSeries(){

		$results = array();

		$positive = 0;
		$negative = 0;
		$neutral = 0;

		$negative_threshold = 20;

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('emotion'))  && $this->request->query('emotion')!="" ){



			$connection = ConnectionManager::get('default');

			$emotionID = -1;

			$qq = "SELECT id FROM emotion_types WHERE label='" . $this->request->query('emotion') . "'";

			if($qq!=""){
				$re1 = $connection->execute($qq)->fetchAll('assoc');
				if($re1 && count($re1)>0){
					$emotionID = $re1[0]["id"];
				}
			}



			if($emotionID!=-1){

				$sentimentcondition = " AND e.emotion_type_id=" . $emotionID;

				$researcharray = explode(",", $this->request->query('researches')  );

				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

				if($this->request->query('mode')!="day"){
					$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
				}

				$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c , emotions e WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND c.id=e.content_id ';

				if( null!==$this->request->query('mode')){

					$interval = "1 YEAR";

					if($this->request->query('mode')=="day"){
						$interval = "1 DAY";					
					} else if($this->request->query('mode')=="week"){
						$interval = "1 WEEK";					
					} else if($this->request->query('mode')=="month"){
						$interval = "1 MONTH";					
					} else if($this->request->query('mode')=="all"){
						$interval = "1 YEAR";					
					}



					$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
				}

				if( null!==$this->request->query('language')){
					$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
				}

				$querystring = $querystring . $sentimentcondition;


				if($this->request->query('mode')!="day"){
					$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at)';
				} else {
					$querystring = $querystring . ' GROUP BY YEAR(created_at) , MONTH(created_at), DAY(created_at), HOUR(created_at)';
				}

				$querystring = $querystring . ' ORDER BY created_at ASC';


				//echo($querystring);

				if($querystring!=""){
					$re = $connection->execute($querystring)->fetchAll('assoc');
				
					foreach ($re as $ce) {

						$y = $ce["y"];
						$m = $ce["m"];
						$d = $ce["d"];
						$h = 0;
						if($this->request->query('mode')=="day"){
							$h = $ce["h"];
						}
						$c = $ce["c"];

						$a = strptime($d . '-' . $m . '-' . $y . " " . $h . ":00", '%d-%m-%Y HH:MM');
						$timestamp = mktime($h, 0, 0, $m, $d, $y);

						//$timestamp = strtotime( $d . '-' . $m . '-' . $y . " " . $h . ":00:00");

						if( null!==$this->request->query('mode') &&  ($this->request->query('mode')!="day")  ){
							$a = strptime($d . '-' . $m . '-' . $y , '%d-%m-%Y');
							$timestamp = mktime($h, 0, 0, $m, $d, $y);
						}

						$results[] = [$timestamp,$c];

					}	
				}
			}


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);


	}




	function getSentiment(){

		$results = array();

		$positive = 0;
		$negative = 0;
		$neutral = 0;

		$negative_threshold = 20;

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT comfort, energy FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') ';

			if( null!==$this->request->query('mode')){

				$interval = "1 YEAR";

				if($this->request->query('mode')=="day"){
					$interval = "1 DAY";					
				} else if($this->request->query('mode')=="week"){
					$interval = "1 WEEK";					
				} else if($this->request->query('mode')=="month"){
					$interval = "1 MONTH";					
				} else if($this->request->query('mode')=="all"){
					$interval = "1 YEAR";					
				}



				$querystring = $querystring . ' AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ';
			}

			if( null!==$this->request->query('language')){
				$querystring = $querystring . ' AND language = \"' . $this->request->query('language')  . '\"';
			}

			$querystring = $querystring . ' ORDER BY id DESC';

			if( null!==$this->request->query('limit')){
				$querystring = $querystring . ' LIMIT 1,' . $this->request->query('limit');
			}

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $ce) {
					if($ce["comfort"] > $negative_threshold){
						$positive = $positive + 1;
					} else if($ce["comfort"] < -$negative_threshold){
						$negative = $negative + 1;
					} else {
						$neutral = $neutral + 1;
					}
				}	
			}


			
		}

		$this->set(compact('positive','negative','neutral'));
		$this->set('_serialize', ['positive','negative','neutral']);

	}

	function getContentMatch(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );



			if(!is_null($this->request->query('q'))  && $this->request->query('q')!="" ){

				$ces = TableRegistry::get('Contents');

				$conditions = [
					'research_id IN' => $researcharray,
					'OR' => [
						['content LIKE' => '% ' . $this->request->query('q') . '%'],
						['content LIKE' => $this->request->query('q') . '%'	],
						['content LIKE' => '% ?' . $this->request->query('q') . '%']
					]
					
				];

				if(!is_null($this->request->query('language'))  && $this->request->query('language')!="" && $this->request->query('language')!="XXX" ){
					$conditions[] = [ "language" => $this->request->query('language') ];
				}

				$q1 = null;

				if(!is_null($this->request->query('limit'))  && $this->request->query('limit')!="" ){
					$q1 = $ces
					    ->find('all')
					    ->select([
					    	'link', 'content', 'created_at', 'lat', 'lng', 'comfort','energy'
					    ])
					    ->where( $conditions )
					    ->limit( $this->request->query('limit') );
				} else {
					$q1 = $ces
					    ->find('all')
					    ->select([
					    	'link', 'content', 'created_at', 'lat', 'lng', 'comfort','energy'
					    ])
					    ->where( $conditions );
				}

				//debug($q1);

				foreach ($q1 as $r) {
					$o = new \stdClass();
					$o->link = $r->link;
					$o->content = $r->content;
					$o->created_at = $r->created_at;
					$o->lat = $r->lat;
					$o->lng = $r->lng;
					$o->comfort = $r->comfort;
					$o->energy = $r->energy;
					$results[] = $o;
				}
			}
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



}