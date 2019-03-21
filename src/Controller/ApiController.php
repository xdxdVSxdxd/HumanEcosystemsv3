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
		$this->Auth->allow( ['getRelations','getWordNetwork' , 'getEmotions', 'getTimeline', 'getEmotionsTimeline', 'getWordCloud' , 'getEnergyComfortDistribution', 'getGeoPoints', 'getGeoEmotionPoints','getHashtagNetwork', 'getHashtagCloud', 'getSentiment','getContentMatch','getImages','getNumberOfSubjects','getRecent','getContentByComfortEnergy','getMaxMinComfortEnergyPerResearch','getImagesByComfortEnergy','getMultipleKeywordsTimeline','getDesireTimeline','getStatistics','getSentimentSeries','getEmotionsSeries','getActivity','getTopUsers','getKeywordSeries',"getEmotionalBoundariesSeries","getMultipleMentionsSeries","getEmotionallyWeightedKeywordSeries", 'getSingleHashtagNetwork', 'getSingleHashtagStatistics','getStatisticsOnResearches','getMultipleKeywordStatistics','getSubjectsForGroups','getMultipleSubjects','getTopSubjects','getPostsPerUserID','getTopicTimeSeries','getMessagesForTagAndDate','getMessagesFromTimeAgo' , 'getLanguageStatistics','getTagsFromToDate','getWordNetworkForWord','addContent'] );
		
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

			$rese = explode(",",$this->request->query('researches') );
			for($i = 0; $i<count($rese); $i++){
				$rese[$i] = intval($rese[$i]);
			}
			$researchlist = implode(",", $rese);
			
			$ncontents = 0;
			$nusers = 0;

			$querystring = 'SELECT count(*) as c FROM ( SELECT  DISTINCT subject_id as s FROM contents c WHERE c.research_id IN ( ' . $researchlist . ') AND created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' ) ) a';

			//echo($querystring);

			if($querystring!=""){

				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$nusers = $v["c"];
					$results["nusers"] = $nusers;
				}	
			}

			$querystring = 'SELECT  count(*) as c FROM contents c WHERE c.research_id IN ( ' . $researchlist . ' ) AND created_at > DATE_SUB(CURDATE(), INTERVAL  ' . $interval . ' ) ';

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



	public function getMessagesFromTimeAgo(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('number'))  && $this->request->query('number')!=""   && !is_null($this->request->query('unit'))  && $this->request->query('unit')!=""    ){



			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			
			$number = intval( $this->request->query('number') );
			$unit = strtoupper( $this->request->query('unit') );

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$interval = "1 DAY";

			if($unit=="SECOND"){
				$interval = $number . " SECOND";
			} else if($unit=="MINUTE"){
				$interval = $number . " MINUTE";
			} else if($unit=="HOUR"){
				$interval = $number . " HOUR";
			}  else if($unit=="DAY"){
				$interval = $number . " DAY";
			}  else if($unit=="WEEK"){
				$interval = $number . " WEEK";
			}  else if($unit=="MONTH"){
				$interval = $number . " MONTH";
			}  else if($unit=="YEAR"){
				$interval = $number . " YEAR";
			}  else {
				$interval = "1 DAY";
			}

			//echo($interval);
			
			$ncontents = 0;
			$nusers = 0;

			$querystring = 'SELECT DISTINCT id, link, content, created_at, lat, lng, comfort, energy FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND created_at > DATE_SUB(NOW(), INTERVAL ' . $interval . ') ';

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$results[] = $v;
				}	
			}

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}




	public function getTagsFromToDate(){

		$results = new \stdClass();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('fromdate'))  && $this->request->query('fromdate')!=""   && !is_null($this->request->query('todate'))  && $this->request->query('todate')!=""    ){



			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			
			$fromdate =  $this->request->query('fromdate');
			$todate = $this->request->query('todate');

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$querystring = "SELECT e.entity as label , count(*) as c, AVG(c.energy) as energy, AVG(c.comfort) as comfort FROM contents c , contents_entities ce , entities e WHERE created_at >= '" . $fromdate . "' AND created_at < '" . $todate . "' AND c.research_id IN ( " .  implode(",", $researcharray) .  " ) AND ce.content_id = c.id AND e.id = ce.entity_id AND e.entity_type_id = 1 GROUP BY e.id ORDER BY c DESC";

			//echo($querystring);

			$resu = array();
			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$o = new \stdClass();
					$o->label = $v["label"];
					$o->c = intval( $v["c"] );
					$o->energy = floatval( $v["energy"] );
					$o->comfort = floatval( $v["comfort"] );
					$resu[] = $o;
				}	
			}
			$results->children = $resu;

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}








	public function addContent(){

		$results = new \stdClass();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('content'))  && $this->request->query('content')!=""    && !is_null($this->request->query('subject'))  && $this->request->query('subject')!=""  && !is_null($this->request->query('language'))  && $this->request->query('language')!=""   ){



			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			
			$content =  $this->request->query('content');
			$content = str_replace("'", "\'", $content);
			$content = str_replace('"', "\"", $content);
			$subject =  $this->request->query('subject');
			$subject = str_replace("'", "\'", $subject);
			$subject = str_replace('"', "\"", $subject);

			$language =  $this->request->query('language');
			$language = str_replace("'", "\'", $language);
			$language = str_replace('"', "\"", $language);

			//use connectionmanager
			$connection = ConnectionManager::get('default');


			// c'è già' l'user "subject"?
			// se no: crealo e prenditi l'ID
			// se si: prenditi l'ID
			$subject_id = -1;
			$querystring = "SELECT id from subjects WHERE name='" . $subject . "' AND research_id IN (" .  implode(",", $researcharray) . ")";
			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$subject_id = $v["id"];
				}	
			}

			if($subject_id==-1){

				$qs2 = "INSERT INTO subjects(research_element_id, research_id, name, social_id, screen_name, location, followers_count, friends_count, listed_count, language, profile_url, profile_image_url) VALUES(-1, " . $researcharray[0] . ", '" . $subject . "', -1, '" . $subject . "', 'NONE', 0, 0, 0, '" . $language . "', '', '' )";

				$re = $connection->execute($qs2);

				// prendi l'id
				$re = $connection->execute("select last_insert_id() as id")->fetchAll('assoc');;
				foreach ($re as $v) {
					$subject_id = $v["id"];
				}

			}

			// memorizza contenuto
			$content_id = -1;

			$qs2 = "INSERT INTO contents(research_id,research_element_id,subject_id,link,content,created_at,social_id,language,favorite_count,retweet_count,lat,lng,comfort,energy) VALUES( " . $researcharray[0] . ",-1," . $subject_id . ",'','" . $content . "',NOW(),-1,'" . $language .   "',0,0,-999,-999,0,0 )";

			$re = $connection->execute($qs2);

			$re = $connection->execute("select last_insert_id() as id");
			foreach ($re as $v) {
				$content_id = $v["id"];
			}

			// elabora e memorizza tag
			preg_match("/#(\\w+)/", $content, $matches);
			if(count($matches)>0){
				// memorizza tags
				foreach ($matches as $m) {
					$id_entity = -1;
					$m = str_replace("'", "\'", $m);
					$m = str_replace('"', "\"", $m);
					$q3 = "SELECT id FROM entites WHERE UPPER(entity)='" . strtoupper( $m ) . "'";
					$re = $connection->execute($q3)->fetchAll('assoc');;
					foreach ($re as $v) {
						$id_entity = $v["id"];
					}
					if($id_entty==-1){
						$q4 = "INSERT INTO entities(entity_type_id,entity) VALUES(1,'" . $m . "')";
						$re = $connection->execute($q4);

						$re = $connection->execute("select last_insert_id() as id");
						foreach ($re as $v) {
							$id_entity = $v["id"];
						}
					}

					$q5 = "INSERT INTO contents_entities(content_id,research_id,research_element_id,entity_id) VALUES (" . $content_id . "," . $researcharray[0] . ",-1," . $id_entity . ")";
					$re = $connection->execute($q5);

				}
				
			}
			// elabora e memorizza emozioni

			$contents = TableRegistry::get('Contents');
			$smileysemotions = TableRegistry::get('SmileyEmotions');
			$wordemotions = TableRegistry::get('WordEmotions');
			$emotions = TableRegistry::get('Emotions');
			$emotiontypes = TableRegistry::get('EmotionTypes');

			$stopwords = new \StopWords();

			$sm = $smileysemotions->find('all');
			$smileys = array();
			foreach($sm as $s){
				$smiley = new \stdClass();
				$smiley->smiley = $s->smiley;
				$smiley->emotion_id = $s->emotion_id;
				$smileys[] = $smiley;
	 		}

	 		$wo = $wordemotions->find('all');
			$wordsem = array();
			foreach($wo as $w){
				$word = new \stdClass();
				$word->word = $w->word;
				$word->emotion_id = $w->emotion_id;
				$wordsem[] = $word;
	 		}

	 		$et = $emotiontypes->find('all');
	 		$etypes = array();
	 		foreach($et as $e){
	 			$ett = new \stdClass();
	 			$ett->id = $e->id;
	 			$ett->comfort = $e->comfort;
	 			$ett->energy = $e->energy;
	 			$etypes[] = $ett;
	 		}

	 		$this->process_emotions($content_id,$researcharray[0],-1,$content,$smileys,$wordsem,$stopwords,$emotions,$etypes,$contents);


			// elabora e memorizza relazioni

			// use connectionmanager end

		}



		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}











	public function getLanguageStatistics(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			
			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$interval = "1 DAY";
			$unit = strtoupper( $this->request->query('unit') );

			if($unit=="SECOND"){
				$interval = $number . " SECOND";
			} else if($unit=="MINUTE"){
				$interval = $number . " MINUTE";
			} else if($unit=="HOUR"){
				$interval = $number . " HOUR";
			}  else if($unit=="DAY"){
				$interval = $number . " DAY";
			}  else if($unit=="WEEK"){
				$interval = $number . " WEEK";
			}  else if($unit=="MONTH"){
				$interval = $number . " MONTH";
			}  else if($unit=="YEAR"){
				$interval = $number . " YEAR";
			}  else {
				$interval = "1 DAY";
			}

			
			$querystring = 'SELECT language, count(*) as n FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND created_at > DATE_SUB(NOW(), INTERVAL ' . $interval . ') GROUP BY language ORDER BY language';

			//echo($querystring);

			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
			
				foreach ($re as $v) {
					$results[] = $v;
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT s1.id as sourceid, s1.screen_name as sourcenick , s1.profile_url as sourceurl, s2.id as targetid, s2.screen_name as targetnick , s2.profile_url as targeturl FROM subjects s1, subjects s2, relations r WHERE r.research_id IN (' .  implode(",", $researcharray) .  ') AND  s1.id=r.subject_1_id AND s2.id=r.subject_2_id ';

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

				$querystring = $querystring . ' AND  ( ( r.subject_1_id IN ( SELECT subject_id as id FROM contents c WHERE c.research_id IN (' .  $this->request->query('researches') .  ') AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') )  )    OR    ( r.subject_1_id IN ( SELECT subject_id as id FROM contents c WHERE c.research_id IN (' . implode(",", $researcharray)  .  ') AND c.created_at > DATE_SUB(CURDATE(), INTERVAL ' . $interval . ') ) )  )  ';
			}

			if( null!==$this->request->query('sensibility')){
				$querystring = $querystring . ' AND r.c>=' . intval($this->request->query('sensibility'));
			}

			$querystring = $querystring . ' ORDER BY r.id DESC ';

			

			
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








	public function getTopicTimeSeries(){
		$results = array();
		
		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT e.entity as entity, count(*) as value, DATE(c.created_at) date FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  implode(",", $researcharray ) .  ') AND  ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 ';

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

			
			$querystring = $querystring . ' GROUP BY entity, date ORDER BY entity asc, date asc ';
			
			if( null!==$this->request->query('gt')){
				$querystring = 'SELECT * FROM (' . $querystring . ') a WHERE value > ' . intval($this->request->query('gt'));
			}



			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $c) {
				
					$o = new \stdClass();
					$o->entity = $c["entity"];
					$o->value = $c["value"];
					$o->date = $c["date"];
					$results[] = $o;

					
				}// foreach

			} //if query empty


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}









	public function getMessagesForTagAndDate(){
		$results = array();
		
		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('day'))  && $this->request->query('day')!=""   && !is_null($this->request->query('month'))  && $this->request->query('month')!=""  && !is_null($this->request->query('year'))  && $this->request->query('year')!=""  &&  !is_null($this->request->query('entity'))  && $this->request->query('entity')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT c.link as link, c.content as content, c.comfort as comfort, c.energy as energy FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  implode(",",$researcharray) .  ') AND YEAR(created_at)=' . intval($this->request->query('year')) . ' AND MONTH(created_at)=' . intval($this->request->query('month')) . ' AND DAY(created_at)=' . intval( $this->request->query('day') ) . ' AND  ce.content_id=c.id AND e.id=ce.entity_id AND e.entity="' . str_replace("'", "", $this->request->query('entity')) . '" ';

			

			
			$querystring = $querystring . ' ORDER BY created_at asc ';
			
			if($querystring!=""){
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $c) {
				
					$o = new \stdClass();
					$o->link = $c["link"];
					$o->content = $c["content"];
					$o->comfort = $c["comfort"];
					$o->energy = $c["energy"];
					$results[] = $o;

					
				}// foreach

			} //if query empty


			
		}

		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}








	public function getMaxMinComfortEnergyPerResearch(){
		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$comforts = array();
			$energies = array();

			
			// return all the latest ones
			$querystring = 'SELECT MAX(comfort) as maxcomfort, MIN(comfort) as mincomfort, MAX(energy) as maxenergy, MIN(energy) as minenergy FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ')';

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
			$delta = intval($this->request->query('delta'));
		}

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$comforts = array();
			$energies = array();

			if(!is_null($this->request->query('comfort'))  && $this->request->query('comfort')!="" ){
				$comforts = explode(",", $this->request->query('comfort'));
				for($i = 0 ; $i<count($comforts);$i++){
					$comforts[$i] = intval($comforts[$i]);
				}
			}

			if(!is_null($this->request->query('energy'))  && $this->request->query('energy')!="" ){
				$energies = explode(",", $this->request->query('energy'));
				for($i = 0 ; $i<count($energies);$i++){
					$energies[$i] = intval($energies[$i]);
				}
			}

			$querystring = "";

			if( count($comforts)>0 && count($energies)>0 && count($comforts)==count($energies)  ){
				$querystring = 'SELECT c.id as id,c.content as content,c.comfort as comfort,c.energy as energy FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND (';

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
				$querystring = 'SELECT c.id as id,c.content as content,c.comfort as comfort,c.energy as energy FROM contents c WHERE c.research_id IN (' . implode(",", $researcharray) .  ') LIMIT 0,100';

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$comforts = array();
			$energies = array();

			if(!is_null($this->request->query('comfort'))  && $this->request->query('comfort')!="" ){
				$comforts = explode(",", $this->request->query('comfort'));
				for($i = 0; $i<count($comforts); $i++){
					$comforts[$i] = intval($comforts[$i]);
				}
			}

			if(!is_null($this->request->query('energy'))  && $this->request->query('energy')!="" ){
				$energies = explode(",", $this->request->query('energy'));
				for($i = 0; $i<count($energies); $i++){
					$energies[$i] = intval($energies[$i]);
				}
			}

			$querystring = "";

			if( count($comforts)>0 && count($energies)>0 && count($comforts)==count($energies)  ){
				$querystring = 'SELECT e.entity as entity ,c.comfort as comfort,c.energy as energy FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND (e.entity LIKE "%jpg" OR e.entity LIKE "%png" ) AND (';

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$re = $connection->execute('SELECT count(*) as c FROM subjects s WHERE s.research_id IN (' .  implode(",", $researcharray) .  ')')->fetchAll('assoc');
			
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			$re = $connection->execute('SELECT count(*) as c FROM contents s WHERE s.research_id IN (' .  implode(",", $researcharray) .  ') AND created_at > DATE_SUB(CURDATE(), INTERVAL 10 MINUTE)')->fetchAll('assoc');
			
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			//use connectionmanager
			$connection = ConnectionManager::get('default');

			$results = array();

			if( null!==$this->request->query('limit')){

				$re = $connection->execute('SELECT e.id as id, e.entity as entity FROM contents_entities ce, entities e WHERE ce.research_id IN (' .  implode(",", $researcharray) .  ') AND e.id=ce.entity_id AND ( e.entity LIKE "%jpg" OR e.entity LIKE "%png" ) ORDER BY ce.id DESC LIMIT 0,' . intval($this->request->query('limit')))->fetchAll('assoc');
			} else {
				$re = $connection->execute('SELECT e.id as id, e.entity as entity FROM contents_entities ce, entities e WHERE ce.research_id IN (' .  implode(",", $researcharray) .  ') AND e.id=ce.entity_id AND ( e.entity LIKE "%jpg" OR e.entity LIKE "%png" )')->fetchAll('assoc');
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

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





	function getWordNetworkForWord(){

		$stopwords = new \StopWords();

		$nodes = array();
		$links = array();

		$results = array();
		$resultsrel = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!=""   &&     !is_null($this->request->query('word'))  && $this->request->query('word')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$startingword = $this->request->query('word');
			$startingword =  strtolower( trim($startingword) );
			$startingword = preg_replace("/[^[:alnum:][:space:]]/u", '', $startingword);
			$startingword = str_replace("  ", " ", $startingword);
			$startingword = substr($startingword, 0, max(0,strlen($startingword)-1) );

			$contents = TableRegistry::get('Contents');

			$q1 = null;

			$conditions = array();
			$conditions['research_id IN'] = $researcharray;
			$conditions['content LIKE'] = "%" . $startingword . "%";
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$contents = TableRegistry::get('Contents');

			for($k = 0; $k<count($et) ; $k++){

				$et[$k] =  str_replace("'", "?",  strtoupper($et[$k]));

				$q1 = null;

				$conditions = array();
				$conditions['Contents.research_id IN'] = $researcharray;
				$conditions['UCASE(Contents.content) LIKE'] =  '%' . $et[$k] . '%';

				if( !is_null($this->request->query('mode'))  && $this->request->query('mode')!=""  ){

					if(  $this->request->query('mode')=="ALL" ){
						$conditions[] = "created_at > DATE_SUB(CURDATE(), INTERVAL 3 YEAR )";
					} else if(  $this->request->query('mode')=="MONTH" ){
						$conditions[] = "created_at > DATE_SUB(CURDATE(), INTERVAL 1 MONTH )";
					} else if(  $this->request->query('mode')=="WEEK" ){
						$conditions[] = "created_at > DATE_SUB(CURDATE(), INTERVAL 1 WEEK )";
					} else if(  $this->request->query('mode')=="DAY" ){
						$conditions[] = "created_at > DATE_SUB(CURDATE(), INTERVAL 1 DAY )";
					}

				}

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}


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
	    			->limit(  intval($this->request->query('limit'))  );
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}


			$querystring = 'SELECT  lat,lng,count(*) as c FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){

				$querystring = $querystring . " AND language='" .  str_replace("'", "",  $this->request->query('language') ) . "'";

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


				$querystring = $querystring . " LIMIT 1," . intval($this->request->query('limit'));


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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}


			$querystring = 'SELECT  et.id as emotion_id, et.label as label, lat,lng,count(*) as c FROM contents c , emotions e, emotion_types et WHERE c.research_id IN (' .  implode(",",$researcharray) .  ') AND e.content_id=c.id AND e.emotion_type_id=et.id ';

			if( null!==$this->request->query('language') &&  $this->request->query('language')!="XXX" ){

				$querystring = $querystring . " AND language='" . str_replace("'", "", $this->request->query('language')) . "'";

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


				$querystring = $querystring . " LIMIT 1," . intval($this->request->query('limit'));


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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT c.id as cid, e.id as eid, e.entity as label FROM contents c, contents_entities ce, entities e WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 ';

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
				$querystring = $querystring . ' LIMIT ' . intval($this->request->query('limit'));
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




	function getSingleHashtagNetwork(){

		$maxweight = 0;

		$nodes = array();
		$links = array();

		$results = array();
		$resultsrel = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" &&  !is_null($this->request->query('topic'))  && $this->request->query('topic')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT c.id as cid, e.id as eid, e.entity as label FROM contents c, contents_entities ce, entities e, contents_entities ce2, entities e2 WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 AND c.id = ce2.content_id AND UCASE(e2.entity) LIKE "' . strtoupper( str_replace('"', "", $this->request->query('topic')) ) . '"  AND  ce2.entity_id=e2.id  ';


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
				$querystring = $querystring . ' LIMIT ' . intval($this->request->query('limit'));
			}
			
			//echo($querystring);
			//$querystring = "";

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




	function getSingleHashtagStatistics(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" &&  !is_null($this->request->query('topic'))  && $this->request->query('topic')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT AVG(c.energy) as energy, AVG(c.comfort) as comfort, c.language as language, count(*) as c FROM contents c, contents_entities ce, entities e, contents_entities ce2, entities e2 WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 AND c.id = ce2.content_id AND UCASE(e2.entity) LIKE "' . strtoupper( str_replace('"', "", $this->request->query('topic')) ) . '"  AND  ce2.entity_id=e2.id  ';


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

			$querystring = $querystring . ' GROUP BY language ORDER BY c.created_at DESC ';

			if( null!==$this->request->query('limit')){
				$querystring = $querystring . ' LIMIT ' . intval($this->request->query('limit'));
			}
			
			//echo($querystring);
			//$querystring = "";

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {

					$rt = array();
					$rt["Number"] = $row["c"];
					$rt["Comfort"] = $row["comfort"];
					$rt["Energy"] = $row["energy"];
					$rt["Language"] = $row["language"];

					$results[] = $rt;
				}

			} //if query empty
			
		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}





	function getStatisticsOnResearches(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT c.research_element_id as research_element_id, c.research_id as research_id, r.name as research_name, re.content as research_element, ret.label as research_element_type, count(*) as number, count(subject_id) as num_subjects, MIN(created_at) as from_date, MAX(created_at) as to_date, AVG(comfort) as comfort, AVG(energy) as energy FROM contents c, researches r, research_elements re, research_element_types ret WHERE c.research_id IN ( '  .  implode(",", $researcharray) .  '  ) AND c.research_id=r.id AND c.research_element_id=re.id AND re.research_element_type_id=ret.id GROUP BY c.research_element_id , c.research_id ORDER BY research_id, research_element_id';

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {

					// do something
					$results[] = $row;
				}

			} //if query empty
			
		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}




	function getMultipleKeywordStatistics(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('keywords'))  && $this->request->query('keywords')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			$keywordarray = explode(",", $this->request->query('keywords')  );
			for($i = 0; $i<count($keywordarray); $i++){
				$keywordarray[$i] = str_replace("'", "?", $keywordarray[$i]);
				$keywordarray[$i] = str_replace('"', "?", $keywordarray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT count(*) as c, AVG(comfort) as comfort, AVG(energy) as energy FROM contents c WHERE c.research_id IN ( '  .  implode(",", $researcharray) .  '  ) AND ( 1=0 ';

			for($i=0; $i<count($keywordarray); $i++){
				$querystring = $querystring . ' OR UCASE(c.content) LIKE "%' . trim( strtoupper( $keywordarray[$i]) ) . '%" ';
			}

			$querystring = $querystring . ' ) ';

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {

					// do something
					$results[] = $row;
				}

			} //if query empty
			
		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}





	function getSubjectsForGroups(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('groups'))  && $this->request->query('groups')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			$groups = $this->request->query('groups');

			//print_r($groups);

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT DISTINCT r.subject_1_id as s1id, r.subject_2_id as s2id ' ;


			for($i = 0; $i<count($groups); $i++){

				$querystring = $querystring . " , CASE WHEN ( ";

				$cond = explode(",",  $groups[$i]["search"] );
				for ($j = 0 ; $j<count($cond); $j++){
					$querystring = $querystring . " UCASE(content) LIKE '" . str_replace("'", "\'", strtoupper($cond[$j]) ) . "' OR ";
				}

				$querystring = $querystring . " 1=0 ) THEN 1 ELSE 0 END as  " . str_replace(" ", "_", $groups[$i]["name"] ) . " ";

			}

			$querystring = $querystring . ' FROM contents c, relations r WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND (c.subject_id=r.subject_1_id OR c.subject_id=r.subject_2_id) ';

			$querystring = $querystring . " AND ( ";

			for($i = 0; $i<count($groups); $i++){

				$cond = explode(",",  $groups[$i]["search"] );
				for ($j = 0 ; $j<count($cond); $j++){
					$querystring = $querystring . " UCASE(content) LIKE '" . str_replace("'", "\'", strtoupper($cond[$j]) ) . "' OR ";
				}

			}

			$querystring = $querystring . " 1=0 ) ";


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

			//echo($querystring);

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {
					// do something
					$results[] = $row;
				}

			} //if query empty
			
		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}







	function getMultipleSubjects(){

		$results = array();

		$emotions = array();
		$groups = array();
		$daysofweek = array();
		$stats = array();

		$mentions = 0;
		$engagements = 0;

		$daysofweek["Mon"] = 0;
		$daysofweek["Tue"] = 0;
		$daysofweek["Wed"] = 0;
		$daysofweek["Thu"] = 0;
		$daysofweek["Fri"] = 0;
		$daysofweek["Sat"] = 0;
		$daysofweek["Sun"] = 0;

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" && !is_null($this->request->query('search'))  && $this->request->query('search')!=""  && !is_null($this->request->query('mention'))  && $this->request->query('mention')!="" && !is_null($this->request->query('groups'))  && $this->request->query('groups')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}
			$searcharray = explode(",", $this->request->query('search')  );
			for($i = 0; $i<count($searcharray); $i++){
				$searcharray[$i] = str_replace("'", "?", $searcharray[$i]);
				$searcharray[$i] = str_replace('"', "?", $searcharray[$i]);
			}
			$mentionarray = explode(",", $this->request->query('mention')  );
			for($i = 0; $i<count($mentionarray); $i++){
				$mentionarray[$i] = str_replace("'", "?", $mentionarray[$i]);
				$mentionarray[$i] = str_replace('"', "?", $mentionarray[$i]);
			}

			$groups = $this->request->query('groups');

			for($i = 0; $i<count($groups);$i++) {
				$groups[$i]["n"] = 0;
			}

			//print_r($groups);


			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT DATE_FORMAT(created_at, "%a") as day, c.content as content, c.comfort as comfort, c.energy as energy , c.favorite_count as favorite_count, c.retweet_count as retweet_count FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND ( 1=0 ';

			foreach ($searcharray as $s) {
				$querystring = $querystring . " OR UCASE(c.content) LIKE '%" .  strtoupper( str_replace("'", " ", $s) )  . "%' ";
			}

			foreach ($mentionarray as $s) {
				$querystring = $querystring . " OR UCASE(c.content) LIKE '%" .  strtoupper( str_replace("'", " ", $s) )  . "%' ";
			}

			$querystring = $querystring . ")";


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

			//echo($querystring);

			$tempCE = array();

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {
					
					$o = new \stdClass();
					$o->comfort = $row["comfort"];
					$o->energy = $row["energy"];
					$tempCE[] = $o;


					for($i = 0; $i<count($groups);$i++) {
						$ss =  explode(",", strtoupper( str_replace("%", "", $groups[$i]["search"]) ) );
						$found = false;
						for($j = 0; $j<count($ss)&&!$found; $j++){
							if(strpos(strtoupper($row["content"]), $ss[$j])){
								$found = true;
							}
						}
						if($found){
							$groups[$i]["n"] = $groups[$i]["n"] + 1;
						}
					}

					$daysofweek[$row["day"]] = $daysofweek[$row["day"]] + 1;

					$mentions++;
					$engagements = $engagements + $row["favorite_count"] + $row["retweet_count"];


					//$results[] = $row;
				}

				$cavg = 0;
				$eavg =0;

				foreach($tempCE as $tce){
					$cavg = $cavg +$tce->comfort;
					$eavg = $eavg +$tce->energy;
				}

				if(count($tempCE)>0){
					$cavg = $cavg / count($tempCE);
					$eavg = $eavg / count($tempCE);
				}

				$stats["comfort-avg"] = $cavg;
				$stats["energy-avg"] = $eavg;
				$stats["mentions"] = $mentions;
				$stats["engagements"] = $engagements; 

			} //if query empty


			$results["emotions"] = $tempCE;
			$results["groups"] = $groups;
			$results["daysofweek"] = $daysofweek;
			$results["stats"] = $stats;

		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}






		function getTopSubjects(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}


			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT s.id as subject_id, s.name as name, s.screen_name as nick, s.followers_count as followers, s.friends_count as friends, s.profile_url as purl, s.profile_image_url as imageurl , count(c.id) as nposts, sum(c.favorite_count) as favorites , sum(retweet_count) as shares FROM subjects s, contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND c.subject_id=s.id ';


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



			$querystring = $querystring . " GROUP BY c.subject_id ORDER BY s.followers_count DESC LIMIT 0,500 ";

			//echo($querystring);

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {

					$results[] = $row;
				}

			} //if query empty


			//$results["emotions"] = $tempCE;

		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getPostsPerUserID(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!=""  && !is_null($this->request->query('subject_id'))  && $this->request->query('subject_id')!=""  ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$subject_id = intval($this->request->query('subject_id'));


			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT c.content as text, c.created_at as created_at FROM contents c WHERE c.research_id IN (' . implode(",", $researcharray) .  ') AND c.subject_id=' . $subject_id . ' ';


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



			$querystring = $querystring . " ORDER BY c.created_at DESC LIMIT 0,500 ";

			//echo($querystring);

			if($querystring!=""){
				
				$re = $connection->execute($querystring)->fetchAll('assoc');
				
				foreach ($re as $row) {

					$results[] = $row;
				}

			} //if query empty


			//$results["emotions"] = $tempCE;

		}


		$this->set(compact('results'));
		$this->set('_serialize', ['results']);

	}



	function getHashtagCloud(){

		$results = array();

		if(!is_null($this->request->query('researches'))  && $this->request->query('researches')!="" ){

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT  e.entity as entity FROM contents c , contents_entities ce , entities e WHERE c.research_id IN (' .  implode(",",$researcharray) .  ') AND ce.content_id=c.id AND e.id=ce.entity_id AND e.entity_type_id=1 ';


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

			if(
				!is_null($this->request->query('minComfort')) &&
				is_numeric($this->request->query('minComfort')) &&

				!is_null($this->request->query('maxComfort')) &&
				is_numeric($this->request->query('maxComfort')) &&

				!is_null($this->request->query('minEnergy')) &&
				is_numeric($this->request->query('minEnergy')) &&

				!is_null($this->request->query('maxEnergy')) &&
				is_numeric($this->request->query('maxEnergy')) 

			){

				$querystring = $querystring . ' AND c.comfort >=' . floatval($this->request->query('minComfort')) .   '  AND c.comfort <=' . floatval($this->request->query('maxComfort')) .   '  AND c.energy >=' . floatval($this->request->query('minEnergy')) .   ' AND c.energy <=' . floatval($this->request->query('maxEnergy')) .   ' ';

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

			$sentimentcondition = " AND c.comfort > " . intval($negative_threshold);
			if($this->request->query('sentiment')=="negative"){
				$sentimentcondition = "AND c.comfort < " . -intval($negative_threshold);
			} else if($this->request->query('sentiment')=="neutral"){
				$sentimentcondition = " AND c.comfort >= " . -intval($negative_threshold) . " AND c.comfort <= " . intval($negative_threshold);
			}

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"', "",  $this->request->query('language') ) . '\"';
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


			$wo = strtoupper($this->request->query('keyword'));
			$wo = str_replace("'", "?", $wo);
			$wo = str_replace('"', "?", $wo);

			$sentimentcondition = " AND UCASE(c.content) LIKE '%" . $wo . "%'";

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"', "", $this->request->query('language') ) . '\"';
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

			$wo = strtoupper($this->request->query('keyword'));
			$wo = str_replace("'", "?", $wo);
			$wo = str_replace('"', "?", $wo);

			$sentimentcondition = " AND UCASE(c.content) LIKE '%" . $wo . "%'";

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}


			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*)*comfort*energy c FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"', "",  $this->request->query('language') ) . '\"';
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


			$emcond = $this->request->query('emotion-condition');
			$emcond = str_replace("'", "", $emcond);
			$emcond = str_replace('"', "", $emcond);
			$emotioncondition = " AND " . $emcond;

			$researcharray = explode(",", $this->request->query('researches')  );
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c WHERE c.research_id IN (' .  implode(",",$researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"', "", $this->request->query('language') ) . '\"';
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT ' . $selector . ' , count(*)';

			if( null!==$this->request->query('weightwith')  && $this->request->query('weightwith')!="" ){
				$ww = $this->request->query('weightwith');
				$ww = str_replace("'", "", $ww);
				$ww = str_replace('"', "", $ww);
				$querystring = $querystring . "*". $ww;
			}

			$querystring = $querystring . ' as c FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"' , "", $this->request->query('language') ) . '\"';
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

			if($this->request->query('mode')!="day"){
				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
			}

			$querystring = 'SELECT HOUR(created_at) as h, count(*) as c FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"', "", $this->request->query('language') ) . '\"';
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');


			$querystring = 'SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, s.followers_count as followers_count, s.listed_count as listed_count, count(*) as c , avg(c.comfort) as avgcomfort, avg(c.energy) as avgenergy, count(*)*(s.followers_count+s.listed_count) as coeff FROM contents c,subjects s WHERE c.research_id IN (' .  implode(",",$researcharray) .  ') AND s.id=c.subject_id ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"',"", $this->request->query('language') ) . '\"';
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

			$qq = "SELECT id FROM emotion_types WHERE label='" . str_replace("'","", $this->request->query('emotion') ). "'";

			if($qq!=""){
				$re1 = $connection->execute($qq)->fetchAll('assoc');
				if($re1 && count($re1)>0){
					$emotionID = $re1[0]["id"];
				}
			}



			if($emotionID!=-1){

				$sentimentcondition = " AND e.emotion_type_id=" . $emotionID;

				$researcharray = explode(",", $this->request->query('researches')  );
				for($i = 0; $i<count($researcharray); $i++){
					$researcharray[$i] = intval($researcharray[$i]);
				}

				$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d, HOUR(created_at) h";

				if($this->request->query('mode')!="day"){
					$selector = "YEAR(created_at) y, MONTH(created_at) m , DAY(created_at) d";
				}

				$querystring = 'SELECT ' . $selector . ' , count(*) c FROM contents c , emotions e WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') AND c.id=e.content_id ';

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
					$querystring = $querystring . ' AND language = \"' . str_replace('"', "", $this->request->query('language') ) . '\"';
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}

			$connection = ConnectionManager::get('default');

			$querystring = 'SELECT comfort, energy FROM contents c WHERE c.research_id IN (' .  implode(",", $researcharray) .  ') ';

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
				$querystring = $querystring . ' AND language = \"' . str_replace('"','',$this->request->query('language'))  . '\"';
			}

			$querystring = $querystring . ' ORDER BY id DESC';

			if( null!==$this->request->query('limit')){
				$querystring = $querystring . ' LIMIT 1,' . intval($this->request->query('limit'));
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
			for($i = 0; $i<count($researcharray); $i++){
				$researcharray[$i] = intval($researcharray[$i]);
			}



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


	function process_emotions($content_id,$research_id,$research_element_id,$text,$smileys,$wordsem,$stopwords,$emotions,$etypes,$contents){
		
		//echo("\n\n<br>-----------<br>\n");
		
		//$text = $text . " happy happytmeglio megliohappy ciao miao bau";

		//echo($text . "<br>");


		// smileys
		$results_smileys = array();

		$comfort_tot = 0;
		$energy_tot = 0;
		
		foreach($smileys as $smiley){
			if( preg_match('#(^|\W)'.preg_quote($smiley->smiley,'#').'($|\W)#', $text)>0 ){
				//found
				if(isset($results_smileys["emo-" . $smiley->emotion_id])){
					$results_smileys["emo-" . $smiley->emotion_id] = $results_smileys["emo-" . $smiley->emotion_id] +1;
				} else {
					$results_smileys["emo-" . $smiley->emotion_id] = 1;
				}

				$found = false;
				for($i = 0; $i<count($etypes)&&!$found; $i++){
					if($etypes[$i]->id==$smiley->emotion_id){
						$found = true;
						$comfort_tot = $comfort_tot + $etypes[$i]->comfort;
						$energy_tot = $energy_tot + $etypes[$i]->energy;
					}
				}

			}
		}
		// smileys end


		// clean string and remove stopwords
			$val = $text;
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

			/*
			for($i = 0; $i<count($stopwords->stopwords); $i++){
				$val = preg_replace('/\b' . $stopwords->stopwords[$i] . '\b/u', ' ', $val);
			}
			*/

			$words = explode(" ", $val);
		// clean string and remove stopwords

		// analisi con words
			$results_words = array();
			foreach ($wordsem as $word) {
				$regexp = '/\b(' .  $word->word . '\w*)\b/';

				//echo($regexp . "<br>");

				if(preg_match($regexp, $text)){
					if(isset($results_words["emo-" . $word->emotion_id])){
						$results_words["emo-" . $word->emotion_id] = $results_words["emo-" . $word->emotion_id] +1;
					} else {
						$results_words["emo-" . $word->emotion_id] = 1;
					}

					$found = false;
					for($i = 0; $i<count($etypes)&&!$found; $i++){
						if($etypes[$i]->id==$word->emotion_id){
							$found = true;
							$comfort_tot = $comfort_tot + $etypes[$i]->comfort;
							$energy_tot = $energy_tot + $etypes[$i]->energy;
						}
					}

				}
			}
		// analisi con words - fine


		foreach ($results_smileys as $key => $value) {

			$key = str_replace("emo-", " ", $key);
			$emo = intval($key);

			$e = $emotions->newEntity();
			$e->research_id = $research_id;
			$e->research_element_id = $research_element_id;
			$e->content_id = $content_id;
			$e->emotion_type_id = $emo;
			$e->c = $value;

			$emotions->save($e);
			
		}

		foreach ($results_words as $key => $value) {

			$key = str_replace("emo-", " ", $key);
			$emo = intval($key);

			$e = $emotions->newEntity();
			$e->research_id = $research_id;
			$e->research_element_id = $research_element_id;
			$e->content_id = $content_id;
			$e->emotion_type_id = $emo;
			$e->c = $value;

			$emotions->save($e);
			
		}

		$ct = $contents->get($content_id);
		$ct->comfort = $comfort_tot;
		$ct->energy = $energy_tot;
		$contents->save($ct);

		/*
		print_r($results_smileys);
		echo("\n<br>\n");
		print_r($results_words);
		echo("\n\n<br><br>\n\n");
		*/

	}


}