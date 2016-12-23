<?php
namespace App\Model\Entity;

//require_once ('../vendor/codebird/codebird.php');
require_once ('../vendor/emotions/stopwords.php');


use Cake\ORM\Entity;
use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Table\ResearchElementsTable;
use App\Model\Entity\ResearchElement;
use App\Model\Table\ResearchesTable;
use App\Model\Entity\Research;
use App\Model\Table\SubjectsTable;
use App\Model\Entity\Subject;
use App\Model\Table\ContentsTable;
use App\Model\Entity\Content;
use App\Model\Table\EntitiesTable;
use App\Model\Entity\Entita;
use Cake\I18n\Time;
use App\Model\Table\ContentsEntitiesTable;
use App\Model\Table\RelationsTable;
use App\Model\Table\SmileyEmotionsTable;
use App\Model\Entity\SmyleyEmotion;
use App\Model\Table\WordEmotionsTable;
use App\Model\Entity\WordEmotion;
use App\Model\Table\EmotionsTable;
use App\Model\Entity\Emotion;
use App\Model\Table\EmotionTypesTable;
use App\Model\Entity\EmotionType;
use Cake\Utility\Inflector;


use InstagramScraper\Instagram;

class ResearchElement extends Entity
{



	function process($id){
		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		//echo("[In Reseaech Element:" . $this->id . "]\n");

		if($this->active==1){
			if($researchelement->research_element_type_id==1){
				//echo("[Element type: 1]");
				$researchelement->twitterkeyword($id);
				$researchelement->instakeyword($id);
			} else if($researchelement->research_element_type_id==2){
				//echo("[Element type: 2]");
				$researchelement->twittermentions($id);
				$researchelement->instamentions($id);
			} else if($researchelement->research_element_type_id==3){
				//echo("[Element type: 3]");
				$researchelement->twitterhashtags($id);
				$researchelement->instahashtags($id);
			} else if($researchelement->research_element_type_id==4){
				//echo("[Element type: 4]");
				$researchelement->fbpage($id);
			} else if($researchelement->research_element_type_id==5){
				//echo("[Element type: 5]");
				$researchelement->twitteruser($id);
			} else if($researchelement->research_element_type_id==6){
				//echo("[Element type: 6]");
				$researchelement->instauser($id);
			}
		}
		// else {
		//	echo("[research element is not active]");
		//}

		$this->autoRender = false;

	}



	// search Twitter for keyword
	function twitterkeyword($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			!is_null($research->twitter_consumer_key) &&
			$research->twitter_consumer_key!="" &&

			!is_null($research->twitter_consumer_secret) &&
			$research->twitter_consumer_secret!="" &&

			!is_null($research->twitter_token) &&
			$research->twitter_token!="" &&

			!is_null($research->twitter_token_secret) &&
			$research->twitter_token_secret!="" &&

			!is_null($research->twitter_bearer_token) &&
			$research->twitter_bearer_token!="" 
		){
			// everything set, I can do the search

			\Codebird\Codebird::setConsumerKey($research->twitter_consumer_key, $research->twitter_consumer_secret );

			$cb = \Codebird\Codebird::getInstance();

			$cb->setToken($research->twitter_token, $research->twitter_token_secret);

			\Codebird\Codebird::setBearerToken( $research->twitter_bearer_token );

			$params = array();

			$contentparts = explode(",", $researchelement->content);
			$querystring = "q=" . implode(" OR ", $contentparts);

			$params["q"] = $querystring;

			if($researchelement->lat!=-999 && $researchelement->lng!=-999 ){
				//$params["geocode"] = $researchelement->lat . "," . $researchelement->lng . ",50mi"; 
				if($params["q"] == "q=*"){
					$params["q"] = "geocode=" . $researchelement->lat . "," . $researchelement->lng . ",50mi";
				} else {
					$params["q"] = $params["q"] . "&geocode=" . $researchelement->lat . "," . $researchelement->lng . ",50mi";
				}
			}

			if($researchelement->language!="XXX"){
				//$params["lang"] = $researchelement->language;
				$params["q"] = $params["q"] . "&lang=" . $researchelement->language;
			}

			//$params["count"] = 100;

			//$params["include_entities"] = true;

			$params["q"] = $params["q"] . "&count=100&include_entities=true";

			$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

			//echo($params["q"]);

			$reply = $cb->search_tweets($params["q"],true);

			//print_r($reply);

			$this->process_tweets($reply,$research->id,$researchelement->id);


		}// end main check

		$this->autoRender = false;

	}

	// search Twitter for mentions
	function twittermentions($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			!is_null($research->twitter_consumer_key) &&
			$research->twitter_consumer_key!="" &&

			!is_null($research->twitter_consumer_secret) &&
			$research->twitter_consumer_secret!="" &&

			!is_null($research->twitter_token) &&
			$research->twitter_token!="" &&

			!is_null($research->twitter_token_secret) &&
			$research->twitter_token_secret!="" &&

			!is_null($research->twitter_bearer_token) &&
			$research->twitter_bearer_token!="" 
		){
			// everything set, I can do the search

			\Codebird\Codebird::setConsumerKey($research->twitter_consumer_key, $research->twitter_consumer_secret );

			$cb = \Codebird\Codebird::getInstance();

			$cb->setToken($research->twitter_token, $research->twitter_token_secret);

			\Codebird\Codebird::setBearerToken( $research->twitter_bearer_token );

			$params = array();

			$contentparts = explode(",", $researchelement->content);
			for($i = 0; $i<count($contentparts); $i++){
				if(substr($contentparts[$i], 0, 1) === '@'){
					// is an hashtag
				} else {
					//convert to hashtag
					$contentparts[$i] = "@" . $contentparts[$i];
				}
			}
			$querystring = "q=" . implode(" OR ", $contentparts);

			//echo($querystring);

			$params["q"] = $querystring;

			if($researchelement->lat!=-999 && $researchelement->lng!=-999 ){
				//$params["geocode"] = $researchelement->lat . "," . $researchelement->lng . ",50mi"; 
				$params["q"] = $params["q"] . "&geocode=" . $researchelement->lat . "," . $researchelement->lng . ",50mi";
			}

			if($researchelement->language!="XXX"){
				//$params["lang"] = $researchelement->language;
				$params["q"] = $params["q"] . "&lang=" . $researchelement->language;
			}

			//$params["count"] = 100;

			//$params["include_entities"] = true;

			$params["q"] = $params["q"] . "&count=100&include_entities=true";

			//echo("[" . $params["q"] . "]");

			$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

			$reply = $cb->search_tweets($params["q"],true);

			//print_r($reply);

			$this->process_tweets($reply,$research->id,$researchelement->id);


		}// end main check

		$this->autoRender = false;

	}

	// search Twitter for hashtags
	function twitterhashtags($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			!is_null($research->twitter_consumer_key) &&
			$research->twitter_consumer_key!="" &&

			!is_null($research->twitter_consumer_secret) &&
			$research->twitter_consumer_secret!="" &&

			!is_null($research->twitter_token) &&
			$research->twitter_token!="" &&

			!is_null($research->twitter_token_secret) &&
			$research->twitter_token_secret!="" &&

			!is_null($research->twitter_bearer_token) &&
			$research->twitter_bearer_token!="" 
		){
			// everything set, I can do the search

			\Codebird\Codebird::setConsumerKey($research->twitter_consumer_key, $research->twitter_consumer_secret );

			$cb = \Codebird\Codebird::getInstance();

			$cb->setToken($research->twitter_token, $research->twitter_token_secret);

			\Codebird\Codebird::setBearerToken( $research->twitter_bearer_token );

			$params = array();

			$contentparts = explode(",", $researchelement->content);
			for($i = 0; $i<count($contentparts); $i++){
				if(substr($contentparts[$i], 0, 1) === '#'){
					// is an hashtag
				} else {
					//convert to hashtag
					$contentparts[$i] = "#" . $contentparts[$i];
				}
			}
			$querystring = "q=" . implode(" OR ", $contentparts);

			//echo($querystring);

			$params["q"] = $querystring;

			if($researchelement->lat!=-999 && $researchelement->lng!=-999 ){
				//$params["geocode"] = $researchelement->lat . "," . $researchelement->lng . ",50mi"; 
				$params["q"] = $params["q"] . "&geocode=" . $researchelement->lat . "," . $researchelement->lng . ",50mi";
			}

			if($researchelement->language!="XXX"){
				//$params["lang"] = $researchelement->language;
				$params["q"] = $params["q"] . "&lang=" . $researchelement->language;
			}

			//$params["count"] = 100;

			//$params["include_entities"] = true;

			$params["q"] = $params["q"] . "&count=100&include_entities=true";

			//echo("[" . $params["q"] . "]");

			$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

			$reply = $cb->search_tweets($params["q"],true);

			//print_r($reply);

			$this->process_tweets($reply,$research->id,$researchelement->id);


		}// end main check

		$this->autoRender = false;

	}

	// get content from Twitter user account 
	function twitteruser($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			!is_null($research->twitter_consumer_key) &&
			$research->twitter_consumer_key!="" &&

			!is_null($research->twitter_consumer_secret) &&
			$research->twitter_consumer_secret!="" &&

			!is_null($research->twitter_token) &&
			$research->twitter_token!="" &&

			!is_null($research->twitter_token_secret) &&
			$research->twitter_token_secret!="" &&

			!is_null($research->twitter_bearer_token) &&
			$research->twitter_bearer_token!="" 
		){
			// everything set, I can do the search

			\Codebird\Codebird::setConsumerKey($research->twitter_consumer_key, $research->twitter_consumer_secret );

			$cb = \Codebird\Codebird::getInstance();

			$cb->setToken($research->twitter_token, $research->twitter_token_secret);

			\Codebird\Codebird::setBearerToken( $research->twitter_bearer_token );

			$params = array();

			$contentparts = explode(",", $researchelement->content);
			
			$querystring = "screen_name=" . $contentparts[0];

			//echo($querystring);

			$params["q"] = $querystring;

			/*
			if($researchelement->lat!=-999 && $researchelement->lng!=-999 ){
				//$params["geocode"] = $researchelement->lat . "," . $researchelement->lng . ",50mi"; 
				$params["q"] = $params["q"] . "&geocode=" . $researchelement->lat . "," . $researchelement->lng . ",50mi";
			}

			if($researchelement->language!="XXX"){
				//$params["lang"] = $researchelement->language;
				$params["q"] = $params["q"] . "&lang=" . $researchelement->language;
			}
			*/

			//$params["count"] = 100;

			//$params["include_entities"] = true;

			$params["q"] = $params["q"] . "&count=200&include_entities=true&exclude_replies=false&include_rts=true";

			//echo("[" . $params["q"] . "]");

			$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

			$api = "statuses/userTimeline";

			$response = $cb->$api($params["q"],true);

			$reply = array();
			$reply["statuses"] = $response;

			//print_r($reply);

			$this->process_tweets($reply,$research->id,$researchelement->id);
			

		}// end main check

		$this->autoRender = false;

	}

	// get Instagram for keyword
	function instakeyword($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			!is_null($research->insta_client_id) &&
			$research->insta_client_id!="" &&

			!is_null($research->insta_token) &&
			$research->insta_token!=""
		){
			// everything set, I can do the search
			$tags = explode(",", $researchelement->content);
			foreach($tags as $tag){
				$tag = Inflector::slug($tag,'');

				//echo("[" . $tag . "]");

				/*
				$urlo = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?access_token=" . $research->insta_token;


				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $urlo);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);

				$json = json_decode(
					//$this->utf8ize(
						$data
					//	)
				);
				*/

				//print_r($json);

				if(trim($tag)!=""){
					//echo("[1]" . $tag);
					$medias = Instagram::getMediasByTag($tag, 30);
					$json2 = new \stdClass();
					$json2->data =$medias;
					
					//$this->process_insta_data($json,$research->id, $researchelement->id,$research->insta_token);
					$this->process_insta_data2($json2,$research->id, $researchelement->id,$research->insta_token,'');
				}
			}

		}// end main check

		$this->autoRender = false;

	}

	// get Instagram for mentions
	function instamentions($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			true
			// TODO: put instagram specific checks
		){
			// everything set, I can do the search

		}// end main check

		$this->autoRender = false;

	}

	// get Instagram for hashtags
	function instahashtags($id){

		$this->instakeyword($id);

	}

	// get content from Instagram user
	function instauser($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			!is_null($research->insta_client_id) &&
			$research->insta_client_id!="" &&

			!is_null($research->insta_token) &&
			$research->insta_token!=""
		){
			// everything set, I can do the search
			$tags = explode(",", $researchelement->content);
			foreach($tags as $tag){
				$tag = Inflector::slug($tag,'_');

				//echo("[" . $tag . "]");

				/*
				$urlo = "https://api.instagram.com/v1/users/" . $tag . "/media/recent/?access_token=" . $research->insta_token;


				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $urlo);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);

				$json = json_decode(
					//$this->utf8ize(
						$data
					//	)
				);

				*/

				//print_r($json);

				try {

					//echo("[" . $tag . "]\n");
					if(trim($tag)!=""){
						$medias = Instagram::getMedias(trim($tag), 30);
						$json2 = new \stdClass();
						$json2->data =$medias;

						$this->process_insta_data2($json2,$research->id, $researchelement->id,$research->insta_token,$tag);	
					}
					
				} catch (\InstagramScraper\Exception\InstagramException $e) {
				    echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
				
				//$this->process_insta_data($json,$research->id, $researchelement->id,$research->insta_token);
				

			}

		}// end main check

		$this->autoRender = false;

	}

	// get content from Facebook page
	function fbpage($id){

		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researches = TableRegistry::get('Researches');
		$research = $researches->get($researchelement->research_id);

		if(
			true
			// TODO: put facebook specific checks
		){
			// everything set, I can do the search

		}// end main check

	}

	function process_tweets($fromTwitter,$research_id, $research_element_id){

		$contents = TableRegistry::get('Contents');
		$subjects = TableRegistry::get('Subjects');
		$relations = TableRegistry::get('Relations');
		$entities = TableRegistry::get('Entities');
		$ce = TableRegistry::get('ContentsEntities');
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

 		//print_r($fromTwitter);


 		$et = $emotiontypes->find('all');
 		$etypes = array();
 		foreach($et as $e){
 			$ett = new \stdClass();
 			$ett->id = $e->id;
 			$ett->comfort = $e->comfort;
 			$ett->energy = $e->energy;
 			$etypes[] = $ett;
 		}

 		/*
 		echo("<br>Word Emotions<br>----------<br>");
 		print_r($wordsem);
 		echo("<br><br><br>");
		*/

		foreach($fromTwitter["statuses"] as $status){
			// do something
			//print_r($status);

			if(isset($status["user"])){

				$id_str = $status["user"]["id_str"];
				$name = $status["user"]["name"];
				$screen_name = $status["user"]["screen_name"];
				$location = $status["user"]["location"];
				$followers_count = $status["user"]["followers_count"];
				$friends_count = $status["user"]["friends_count"];
				$listed_count = $status["user"]["listed_count"];
				$lang =  strtolower( $status["user"]["lang"] );
				$profile_url = "https://twitter.com/" . $status["user"]["screen_name"];
				$profile_image_url = $status["user"]["profile_image_url"];


				$query = $subjects->find('all', [
				    'conditions' => ['profile_url =' => $profile_url]
				]);

				$subject = $query->first();

				if(is_null($subject)){
					$subject = $subjects->newEntity();
				}

				$subject->research_element_id = $research_element_id;
				$subject->research_id = $research_id;
				$subject->social_id = $id_str;
				$subject->name = $name;
				$subject->screen_name = $screen_name;
				$subject->location = $location;
				$subject->followers_count = $followers_count;
				$subject->friends_count = $friends_count;
				$subject->listed_count = $listed_count;
				$subject->language = $lang;
				$subject->profile_url = $profile_url;
				$subject->profile_image_url = $profile_image_url;

				if($subjects->save($subject)){
					// continuare
					$link = "https://twitter.com/" . $screen_name . "/status/" . $status["id_str"];
					$text = $status["text"];
					$created_at = new Time( $status["created_at"] );
					$social_id = $status["id_str"];
					$language = $status["lang"];
					$favorite_count = $status["favorite_count"];
					$retweet_count = $status["retweet_count"];

					$lat = -999;
					$lng = -999;

					if(isset($status["geo"]) && is_array($status["geo"])){
						if($status["geo"]["type"]=="Point"){
							$lat = $status["geo"]["coordinates"][0];
							$lng = $status["geo"]["coordinates"][1];
						}
					} else if(isset($status["coordinates"]) && is_array($status["coordinates"])){
						if($status["coordinates"]["type"]=="Point"){
							$lat = $status["geo"]["coordinates"][1];
							$lng = $status["geo"]["coordinates"][0];
						}
					}

					$query = $contents->find('all', [
					    'conditions' => ['link =' => $link]
					]);

					$content = $query->first();

					$isNewContent = false;

					if(is_null($content)){
						$content = $contents->newEntity();
						$isNewContent = true;
					}

					$content->research_id = $research_id;
					$content->research_element_id = $research_element_id;
					$content->subject_id = $subject->id;
					$content->link = $link;
					$content->content = $text;
					$content->created_at = $created_at;
					$content->social_id = $social_id;
					$content->language = $language;
					$content->favorite_count = $favorite_count;
					$content->retweet_count = $retweet_count;

					if($content->retweeted && isset($content->retweeted_status) ){
						$content->favorite_count = 0;
						$content->retweet_count = 0;

						$processRetweet = array();
						$processRetweet["statuses"] = array();
						$processRetweet["statuses"][] = $content->retweeted_status;

						//rielaborare?
						
					}

					$content->lat = $lat;
					$content->lng = $lng;

					if($contents->save($content)){


						if($isNewContent){
							$this->process_emotions($content->id,$research_id,$research_element_id,$content->content,$smileys,$wordsem,$stopwords,$emotions,$etypes,$contents);
						}
						
						// hashtags
						foreach($status["entities"]["hashtags"] as $hashtag){
							//
							$entity_type_id = 1;
							$text = $hashtag["text"];

							$query = $entities->find('all', [
							    'conditions' => [
							    	'entity =' => $text,
							    	'entity_type_id =' => $entity_type_id
							    ]
							]);

							$entity = $query->first();

							if(is_null($entity)){
								$entity = $entities->newEntity();
							}

							$entity->entity_type_id = $entity_type_id;
							$entity->entity = $text;

							if($entities->save($entity)){

								$query = $ce->find('all', [
								    'conditions' => [
								    	'content_id =' => $content->id,
								    	'entity_id =' => $entity->id
								    ]
								]);

								$cee = $query->first();

								if(is_null($cee)){
									$cee = $ce->newEntity();
									$cee->content_id = $content->id;
									$cee->entity_id = $entity->id;
									$cee->research_id = $research_id;
									$cee->research_element_id = $research_element_id;
									$ce->save($cee);
								}
							}

						}
						//heashtags


						// urls
						foreach($status["entities"]["urls"] as $urlentity){
							//
							$entity_type_id = 2;
							$text = $urlentity["url"];

							$query = $entities->find('all', [
							    'conditions' => [
							    	'entity =' => $text,
							    	'entity_type_id =' => $entity_type_id
							    ]
							]);

							$entity = $query->first();

							if(is_null($entity)){
								$entity = $entities->newEntity();
							}

							$entity->entity_type_id = $entity_type_id;
							$entity->entity = $text;

							if($entities->save($entity)){

								$query = $ce->find('all', [
								    'conditions' => [
								    	'content_id =' => $content->id,
								    	'entity_id =' => $entity->id
								    ]
								]);

								$cee = $query->first();

								if(is_null($cee)){
									$cee = $ce->newEntity();
									$cee->content_id = $content->id;
									$cee->entity_id = $entity->id;
									$cee->research_id = $research_id;
									$cee->research_element_id = $research_element_id;
									$ce->save($cee);
								}
							}

						}
						//urls


						// media
						if(isset($status["entities"]["media"]) && is_array($status["entities"]["media"])){
							foreach($status["entities"]["media"] as $mediaentity){
								//
								$entity_type_id = 3;
								$text = $mediaentity["media_url_https"];

								$query = $entities->find('all', [
								    'conditions' => [
								    	'entity =' => $text,
								    	'entity_type_id =' => $entity_type_id
								    ]
								]);

								$entity = $query->first();

								if(is_null($entity)){
									$entity = $entities->newEntity();
								}

								$entity->entity_type_id = $entity_type_id;
								$entity->entity = $text;

								if($entities->save($entity)){

									$query = $ce->find('all', [
									    'conditions' => [
									    	'content_id =' => $content->id,
									    	'entity_id =' => $entity->id
									    ]
									]);

									$cee = $query->first();

									if(is_null($cee)){
										$cee = $ce->newEntity();
										$cee->content_id = $content->id;
										$cee->entity_id = $entity->id;
										$cee->research_id = $research_id;
										$cee->research_element_id = $research_element_id;
										$ce->save($cee);
									}
								}

							}
						}
						//media


						// relations con retweet, quote e mentions
							
							//con retweet - inizio
							if(isset($status["retweeted_status"]) && is_array($status["retweeted_status"])){

								$id_str = $status["retweeted_status"]["user"]["id_str"];
								$name = $status["retweeted_status"]["user"]["name"];
								$screen_name = $status["retweeted_status"]["user"]["screen_name"];
								$location = $status["retweeted_status"]["user"]["location"];
								$followers_count = $status["retweeted_status"]["user"]["followers_count"];
								$friends_count = $status["retweeted_status"]["user"]["friends_count"];
								$listed_count = $status["retweeted_status"]["user"]["listed_count"];
								$lang =  strtolower( $status["retweeted_status"]["user"]["lang"] );
								$profile_url = "https://twitter.com/" . $status["retweeted_status"]["user"]["screen_name"];
								$profile_image_url = $status["retweeted_status"]["user"]["profile_image_url"];


								$query = $subjects->find('all', [
								    'conditions' => ['profile_url =' => $profile_url]
								]);

								$subject2 = $query->first();

								if(is_null($subject2)){
									$subject2 = $subjects->newEntity();
								}

								$subject2->research_element_id = $research_element_id;
								$subject2->research_id = $research_id;
								$subject2->social_id = $id_str;
								$subject2->name = $name;
								$subject2->screen_name = $screen_name;
								$subject2->location = $location;
								$subject2->followers_count = $followers_count;
								$subject2->friends_count = $friends_count;
								$subject2->listed_count = $listed_count;
								$subject2->language = $lang;
								$subject2->profile_url = $profile_url;
								$subject2->profile_image_url = $profile_image_url;

								if($subjects->save($subject2)){
									//
									$query = $relations->find('all', [
									    'conditions' => [
									    	'research_element_id =' => $research_element_id,
									    	'research_id =' => $research_id,
									    	'subject_1_id' => $subject->id,
									    	'subject_2_id' => $subject2->id
									    ]
									]);

									$relation = $query->first();

									if(is_null($relation)){
										$relation = $relations->newEntity();
										$relation->research_id = $research_id;
										$relation->research_element_id = $research_element_id;
										$relation->subject_1_id = $subject->id;
										$relation->subject_2_id = $subject2->id;
										$relation->c = 1;
									} else {
										if($isNewContent){
											$relation->c = $relation->c + 1;
										}
									}

									$relations->save($relation);
								}
							}
							//con retweet - fine

							//con quotes - inizio
							if(isset($status["quoted_status"]) && is_array($status["quoted_status"])){

								$id_str = $status["quoted_status"]["user"]["id_str"];
								$name = $status["quoted_status"]["user"]["name"];
								$screen_name = $status["quoted_status"]["user"]["screen_name"];
								$location = $status["quoted_status"]["user"]["location"];
								$followers_count = $status["quoted_status"]["user"]["followers_count"];
								$friends_count = $status["quoted_status"]["user"]["friends_count"];
								$listed_count = $status["quoted_status"]["user"]["listed_count"];
								$lang =  strtolower( $status["quoted_status"]["user"]["lang"] );
								$profile_url = "https://twitter.com/" . $status["quoted_status"]["user"]["screen_name"];
								$profile_image_url = $status["quoted_status"]["user"]["profile_image_url"];


								$query = $subjects->find('all', [
								    'conditions' => ['profile_url =' => $profile_url]
								]);

								$subject2 = $query->first();

								if(is_null($subject2)){
									$subject2 = $subjects->newEntity();
								}

								$subject2->research_element_id = $research_element_id;
								$subject2->research_id = $research_id;
								$subject2->social_id = $id_str;
								$subject2->name = $name;
								$subject2->screen_name = $screen_name;
								$subject2->location = $location;
								$subject2->followers_count = $followers_count;
								$subject2->friends_count = $friends_count;
								$subject2->listed_count = $listed_count;
								$subject2->language = $lang;
								$subject2->profile_url = $profile_url;
								$subject2->profile_image_url = $profile_image_url;

								if($subjects->save($subject2)){
									//
									$query = $relations->find('all', [
									    'conditions' => [
									    	'research_element_id =' => $research_element_id,
									    	'research_id =' => $research_id,
									    	'subject_1_id' => $subject->id,
									    	'subject_2_id' => $subject2->id
									    ]
									]);

									$relation = $query->first();

									if(is_null($relation)){
										$relation = $relations->newEntity();
										$relation->research_id = $research_id;
										$relation->research_element_id = $research_element_id;
										$relation->subject_1_id = $subject->id;
										$relation->subject_2_id = $subject2->id;
										$relation->c = 1;
									} else {
										if($isNewContent){
											$relation->c = $relation->c + 1;
										}
									}

									$relations->save($relation);
								}
							}
							//con quotes - fine

							//con reply - inizio
							if(isset($status["in_reply_to_user_id_str"]) && isset($status["in_reply_to_screen_name"])){

								$id_str = $status["in_reply_to_user_id_str"];
								$name = $status["in_reply_to_screen_name"];
								$screen_name = $status["in_reply_to_screen_name"];
								$location = "";
								$followers_count = 0;
								$friends_count = 0;
								$listed_count = 0;
								$lang =  "XXX";
								$profile_url = "https://twitter.com/" . $status["in_reply_to_screen_name"];
								$profile_image_url = "";


								$query = $subjects->find('all', [
								    'conditions' => ['profile_url =' => $profile_url]
								]);

								$subject2 = $query->first();

								if(is_null($subject2)){
									$subject2 = $subjects->newEntity();
								}

								$subject2->research_element_id = $research_element_id;
								$subject2->research_id = $research_id;
								$subject2->social_id = $id_str;
								$subject2->name = $name;
								$subject2->screen_name = $screen_name;
								$subject2->location = $location;
								$subject2->followers_count = $followers_count;
								$subject2->friends_count = $friends_count;
								$subject2->listed_count = $listed_count;
								$subject2->language = $lang;
								$subject2->profile_url = $profile_url;
								$subject2->profile_image_url = $profile_image_url;

								if($subjects->save($subject2)){
									//
									$query = $relations->find('all', [
									    'conditions' => [
									    	'research_element_id =' => $research_element_id,
									    	'research_id =' => $research_id,
									    	'subject_1_id' => $subject->id,
									    	'subject_2_id' => $subject2->id
									    ]
									]);

									$relation = $query->first();

									if(is_null($relation)){
										$relation = $relations->newEntity();
										$relation->research_id = $research_id;
										$relation->research_element_id = $research_element_id;
										$relation->subject_1_id = $subject->id;
										$relation->subject_2_id = $subject2->id;
										$relation->c = 1;
									} else {
										if($isNewContent){
											$relation->c = $relation->c + 1;
										}
									}

									$relations->save($relation);
								}
							}
							//con reply - fine

							//con mentions - inizio
							if(isset($status["entities"]["user_mentions"]) && is_array($status["entities"]["user_mentions"])){

								foreach($status["entities"]["user_mentions"] as $mention){

									$id_str = $mention["id_str"];
									$name = $mention["name"];
									$screen_name = $mention["screen_name"];
									$location = "";
									$followers_count = 0;
									$friends_count = 0;
									$listed_count = 0;
									$lang =  "XXX";
									$profile_url = "https://twitter.com/" . $mention["screen_name"];
									$profile_image_url = "";


									$query = $subjects->find('all', [
									    'conditions' => ['profile_url =' => $profile_url]
									]);

									$subject2 = $query->first();

									if(is_null($subject2)){
										$subject2 = $subjects->newEntity();
									}

									$subject2->research_element_id = $research_element_id;
									$subject2->research_id = $research_id;
									$subject2->social_id = $id_str;
									$subject2->name = $name;
									$subject2->screen_name = $screen_name;
									$subject2->profile_url = $profile_url;

									if($subjects->save($subject2)){
										//
										$query = $relations->find('all', [
										    'conditions' => [
										    	'research_element_id =' => $research_element_id,
										    	'research_id =' => $research_id,
										    	'subject_1_id' => $subject->id,
										    	'subject_2_id' => $subject2->id
										    ]
										]);

										$relation = $query->first();

										if(is_null($relation)){
											$relation = $relations->newEntity();
											$relation->research_id = $research_id;
											$relation->research_element_id = $research_element_id;
											$relation->subject_1_id = $subject->id;
											$relation->subject_2_id = $subject2->id;
											$relation->c = 1;
										} else {
											if($isNewContent){
												$relation->c = $relation->c + 1;
											}
										}

										$relations->save($relation);
									}

								}

							}
							//con mentions - fine

						// relations con retweet, quote e mentions
						

					}


				}




			}

			//echo("\n\n-----------------\n\n");
		}
	}





	//process-insta-data
	function process_insta_data($data,$research_id, $research_element_id,$insta_token){

		$contents = TableRegistry::get('Contents');
		$subjects = TableRegistry::get('Subjects');
		$relations = TableRegistry::get('Relations');
		$entities = TableRegistry::get('Entities');
		$ce = TableRegistry::get('ContentsEntities');
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

 		/*
 		echo("<br>Word Emotions<br>----------<br>");
 		print_r($wordsem);
 		echo("<br><br><br>");
		*/


 		if(isset($data->data) &&  ( is_array($data->data) ||  is_object($data->data) )  ){
			foreach($data->data as $status){
				// do something
				//print_r($status);

				if(isset($status->user)){

					$id_str = $status->user->id;
					$name = $status->user->full_name;
					$screen_name = $status->user->username;
					$location = "";
					$followers_count = 0;
					$friends_count = 0;
					$listed_count = 0;
					$lang =  "XXX";//strtolower( $status["user"]["lang"] );
					$profile_url = "https://www.instagram.com/" . $status->user->username . "/";
					$profile_image_url = $status->user->profile_picture;


					$query = $subjects->find('all', [
					    'conditions' => ['profile_url =' => $profile_url]
					]);

					$subject = $query->first();

					if(is_null($subject)){
						$subject = $subjects->newEntity();
					}

					$subject->research_element_id = $research_element_id;
					$subject->research_id = $research_id;
					$subject->social_id = $id_str;
					$subject->name = $name;
					$subject->screen_name = $screen_name;
					$subject->location = $location;
					$subject->followers_count = $followers_count;
					$subject->friends_count = $friends_count;
					$subject->listed_count = $listed_count;
					$subject->language = $lang;
					$subject->profile_url = $profile_url;
					$subject->profile_image_url = $profile_image_url;

					if($subjects->save($subject)){
						// continuare
						$link = $status->link;
						$text = "";
						if(isset($status->caption) && isset($status->caption->text) ){
							$text = $status->caption->text;
						}
						$created_at = new Time( $status->created_time );
						$social_id = $status->id;
						$language = "XXX";
						$favorite_count = 0;
						if(isset($status->likes)){
							$favorite_count = $status->likes->count;
						}
						$retweet_count = 0;

						$lat = -999;
						$lng = -999;

						if(isset($status->location) && isset($status->location->latitude)){
								$lat = $status->location->latitude;
								$lng = $status->location->longitude;
						}

						$query = $contents->find('all', [
						    'conditions' => ['link =' => $link]
						]);

						$content = $query->first();

						$isNewContent = false;

						if(is_null($content)){
							$content = $contents->newEntity();
							$isNewContent = true;
						}

						$content->research_id = $research_id;
						$content->research_element_id = $research_element_id;
						$content->subject_id = $subject->id;
						$content->link = $link;
						$content->content = $text;
						$content->created_at = $created_at;
						$content->social_id = $social_id;
						$content->language = $language;
						$content->favorite_count = $favorite_count;
						$content->retweet_count = $retweet_count;
						$content->lat = $lat;
						$content->lng = $lng;

						if($contents->save($content)){


							if($isNewContent){
								$this->process_emotions($content->id,$research_id,$research_element_id,$content->content,$smileys,$wordsem,$stopwords,$emotions,$etypes,$contents);
							}
							
							// hashtags
							foreach($status->tags as $hashtag){
								//
								$entity_type_id = 1;
								$text = $hashtag;

								$query = $entities->find('all', [
								    'conditions' => [
								    	'entity =' => $text,
								    	'entity_type_id =' => $entity_type_id
								    ]
								]);

								$entity = $query->first();

								if(is_null($entity)){
									$entity = $entities->newEntity();
								}

								$entity->entity_type_id = $entity_type_id;
								$entity->entity = $text;

								if($entities->save($entity)){

									$query = $ce->find('all', [
									    'conditions' => [
									    	'content_id =' => $content->id,
									    	'entity_id =' => $entity->id
									    ]
									]);

									$cee = $query->first();

									if(is_null($cee)){
										$cee = $ce->newEntity();
										$cee->content_id = $content->id;
										$cee->entity_id = $entity->id;
										$cee->research_id = $research_id;
										$cee->research_element_id = $research_element_id;
										$ce->save($cee);
									}
								}

							}
							//heashtags


							// urls

							$urlos = array();
							if(isset($status->images->standard_resolution)){
								$urlos[] = $status->images->standard_resolution->url;
							}

							if(isset($status->images->low_resolution)){
								$urlos[] = $status->images->low_resolution->url;
							}

							if(isset($status->images->thumbnail)){
								$urlos[] = $status->images->thumbnail->url;
							}

							foreach($urlos as $urlentity){
								//
								$entity_type_id = 2;
								$text = $urlentity;

								$query = $entities->find('all', [
								    'conditions' => [
								    	'entity =' => $text,
								    	'entity_type_id =' => $entity_type_id
								    ]
								]);

								$entity = $query->first();

								if(is_null($entity)){
									$entity = $entities->newEntity();
								}

								$entity->entity_type_id = $entity_type_id;
								$entity->entity = $text;

								if($entities->save($entity)){

									$query = $ce->find('all', [
									    'conditions' => [
									    	'content_id =' => $content->id,
									    	'entity_id =' => $entity->id
									    ]
									]);

									$cee = $query->first();

									if(is_null($cee)){
										$cee = $ce->newEntity();
										$cee->content_id = $content->id;
										$cee->entity_id = $entity->id;
										$cee->research_id = $research_id;
										$cee->research_element_id = $research_element_id;
										$ce->save($cee);
									}
								}

							}
							//urls


							// media
							if(isset($urlos)){
								foreach($urlos as $mediaentity){
									//
									$entity_type_id = 3;
									$text = $mediaentity;

									$query = $entities->find('all', [
									    'conditions' => [
									    	'entity =' => $text,
									    	'entity_type_id =' => $entity_type_id
									    ]
									]);

									$entity = $query->first();

									if(is_null($entity)){
										$entity = $entities->newEntity();
									}

									$entity->entity_type_id = $entity_type_id;
									$entity->entity = $text;

									if($entities->save($entity)){

										$query = $ce->find('all', [
										    'conditions' => [
										    	'content_id =' => $content->id,
										    	'entity_id =' => $entity->id
										    ]
										]);

										$cee = $query->first();

										if(is_null($cee)){
											$cee = $ce->newEntity();
											$cee->content_id = $content->id;
											$cee->entity_id = $entity->id;
											$cee->research_id = $research_id;
											$cee->research_element_id = $research_element_id;
											$ce->save($cee);
										}
									}

								}
							}
							//media


							// relations con mentions e comments


								//con mentions - inizio
								if(isset($status->users_in_photo) && is_array($status->users_in_photo)){

									foreach($status->users_in_photo as $mention){

										$id_str = $mention->user->id;
										$name = $mention->user->full_name;
										$screen_name = $mention->user->username;
										$location = "";
										$followers_count = 0;
										$friends_count = 0;
										$listed_count = 0;
										$lang =  "XXX";
										$profile_url = "https://www.instagram.com/" . $mention->user->username . "/";;
										$profile_image_url = "";
										if(isset($mention->user->profile_picture)){
											$profile_image_url = $mention->user->profile_picture;	
										}
										


										$query = $subjects->find('all', [
										    'conditions' => ['profile_url =' => $profile_url]
										]);

										$subject2 = $query->first();

										if(is_null($subject2)){
											$subject2 = $subjects->newEntity();
										}

										$subject2->research_element_id = $research_element_id;
										$subject2->research_id = $research_id;
										$subject2->social_id = $id_str;
										$subject2->name = $name;
										$subject2->screen_name = $screen_name;
										$subject2->profile_url = $profile_url;

										if($subjects->save($subject2)){
											//
											$query = $relations->find('all', [
											    'conditions' => [
											    	'research_element_id =' => $research_element_id,
											    	'research_id =' => $research_id,
											    	'subject_1_id' => $subject->id,
											    	'subject_2_id' => $subject2->id
											    ]
											]);

											$relation = $query->first();

											if(is_null($relation)){
												$relation = $relations->newEntity();
												$relation->research_id = $research_id;
												$relation->research_element_id = $research_element_id;
												$relation->subject_1_id = $subject->id;
												$relation->subject_2_id = $subject2->id;
												$relation->c = 1;
											} else {
												if($isNewContent){
													$relation->c = $relation->c + 1;
												}
											}

											$relations->save($relation);
										}

									}

								}
								//con mentions - fine


								//con comments
								$urlcomments = "https://api.instagram.com/v1/media/" . $content->social_id . "/comments?access_token=" . $insta_token;

								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $urlcomments);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								$datacomments = curl_exec($ch);
								curl_close($ch);

								$jsondatacomments = json_decode(
									//$this->utf8ize(
										$datacomments
									//	)
								);

								//print_r($jsondatacomments);

								if(isset($jsondatacomments) && isset($jsondatacomments->data) && is_array($jsondatacomments->data)){
									foreach($jsondatacomments->data as $comment){


										$id_str = $comment->from->id;
										$name = $comment->from->full_name;
										$screen_name = $comment->from->username;
										$location = "";
										$followers_count = 0;
										$friends_count = 0;
										$listed_count = 0;
										$lang =  "XXX";
										$profile_url = "https://www.instagram.com/" . $comment->from->username . "/";;
										$profile_image_url = "";
										if(isset($comment->from->profile_picture)){
											$profile_image_url = $comment->from->profile_picture;	
										}
										


										$query = $subjects->find('all', [
										    'conditions' => ['profile_url =' => $profile_url]
										]);

										$subject2 = $query->first();

										if(is_null($subject2)){
											$subject2 = $subjects->newEntity();
										}

										$subject2->research_element_id = $research_element_id;
										$subject2->research_id = $research_id;
										$subject2->social_id = $id_str;
										$subject2->name = $name;
										$subject2->screen_name = $screen_name;
										$subject2->profile_url = $profile_url;

										if($subjects->save($subject2)){
											//
											$query = $relations->find('all', [
											    'conditions' => [
											    	'research_element_id =' => $research_element_id,
											    	'research_id =' => $research_id,
											    	'subject_1_id' => $subject->id,
											    	'subject_2_id' => $subject2->id
											    ]
											]);

											$relation = $query->first();

											if(is_null($relation)){
												$relation = $relations->newEntity();
												$relation->research_id = $research_id;
												$relation->research_element_id = $research_element_id;
												$relation->subject_1_id = $subject->id;
												$relation->subject_2_id = $subject2->id;
												$relation->c = 1;
											} else {
												if($isNewContent){
													$relation->c = $relation->c + 1;
												}
											}

											$relations->save($relation);
										}

									}
								}

								// con comments - fine

							// relations con retweet, quote e mentions
							

						}


					}




				}

				//echo("\n\n-----------------\n\n");
			}
		}
	}
	//end process_insta_data






	//process-insta-data2 - scraper
	// TODO: adeguare la procedura ai risultati dello scraper
	function process_insta_data2($data,$research_id, $research_element_id,$insta_token,$owner=''){

		$contents = TableRegistry::get('Contents');
		$subjects = TableRegistry::get('Subjects');
		$relations = TableRegistry::get('Relations');
		$entities = TableRegistry::get('Entities');
		$ce = TableRegistry::get('ContentsEntities');
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

 		/*
 		echo("<br>Word Emotions<br>----------<br>");
 		print_r($wordsem);
 		echo("<br><br><br>");
		*/


 		if(isset($data->data) &&  ( is_array($data->data) ||  is_object($data->data) )  ){
			foreach($data->data as $status){
				// do something
				//print_r($status);

				$iu = null;

				if(is_null($status->ownerId) || $status->ownerId=="" ){
					$iu = Instagram::getAccount($owner);
				} else {
					try{
						if(isset($status->ownerId) && $status->ownerId!="" ){
							$iu = Instagram::getAccountById($status->ownerId);
						}
					} catch (\InstagramScraper\Exception\InstagramException $e) {
					    echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
				}
				//print_r($iu);

				if(isset($iu->id) && ($iu->id==$status->ownerId || is_null($status->ownerId) || $status->ownerId==""  )  ){

					$id_str = $iu->id;
					$name = (is_null($iu->fullName)?"":$iu->fullName);
					$screen_name = $iu->username;
					$location = "";
					$followers_count = $iu->followedByCount;
					$friends_count = $iu->followsCount;
					$listed_count = 0;
					$lang =  "XXX";//strtolower( $status["user"]["lang"] );
					$profile_url = "https://www.instagram.com/" . $screen_name . "/";
					$profile_image_url = $iu->profilePicUrl;


					$query = $subjects->find('all', [
					    'conditions' => ['profile_url =' => $profile_url]
					]);

					$subject = $query->first();

					if(is_null($subject)){
						$subject = $subjects->newEntity();
					}

					$subject->research_element_id = $research_element_id;
					$subject->research_id = $research_id;
					$subject->social_id = $id_str;
					$subject->name = $name;
					$subject->screen_name = $screen_name;
					$subject->location = $location;
					$subject->followers_count = $followers_count;
					$subject->friends_count = $friends_count;
					$subject->listed_count = $listed_count;
					$subject->language = $lang;
					$subject->profile_url = $profile_url;
					$subject->profile_image_url = $profile_image_url;

					if($subjects->save($subject)){
						// continuare
						$link = $status->link;
						$text = "";
						if(isset($status->caption)  ){
							$text = $status->caption;
						}
						$created_at = new Time( $status->createdTime );
						$social_id = $status->id;
						$language = "XXX";
						$favorite_count = 0;
						if(isset($status->likesCount)){
							$favorite_count = $status->likesCount;
						}
						$retweet_count = 0;

						$lat = -999;
						$lng = -999;

						if(isset($status->location) && isset($status->location->latitude)){
								$lat = $status->location->latitude;
								$lng = $status->location->longitude;
						}

						$query = $contents->find('all', [
						    'conditions' => ['link =' => $link]
						]);

						$content = $query->first();

						$isNewContent = false;

						if(is_null($content)){
							$content = $contents->newEntity();
							$isNewContent = true;
						}

						$content->research_id = $research_id;
						$content->research_element_id = $research_element_id;
						$content->subject_id = $subject->id;
						$content->link = $link;
						$content->content = $text;
						$content->created_at = $created_at;
						$content->social_id = $social_id;
						$content->language = $language;
						$content->favorite_count = $favorite_count;
						$content->retweet_count = $retweet_count;
						$content->lat = $lat;
						$content->lng = $lng;

						if($contents->save($content)){


							if($isNewContent){
								$this->process_emotions($content->id,$research_id,$research_element_id,$content->content,$smileys,$wordsem,$stopwords,$emotions,$etypes,$contents);
							}
							
							// hashtags
							preg_match_all('/#([^\s]+)/', $content->content, $hashtags);

							foreach($hashtags[1] as $hashtag){
								//
								$entity_type_id = 1;
								$text = $hashtag;

								$query = $entities->find('all', [
								    'conditions' => [
								    	'entity =' => $text,
								    	'entity_type_id =' => $entity_type_id
								    ]
								]);

								$entity = $query->first();

								if(is_null($entity)){
									$entity = $entities->newEntity();
								}

								$entity->entity_type_id = $entity_type_id;
								$entity->entity = $text;

								if($entities->save($entity)){

									$query = $ce->find('all', [
									    'conditions' => [
									    	'content_id =' => $content->id,
									    	'entity_id =' => $entity->id
									    ]
									]);

									$cee = $query->first();

									if(is_null($cee)){
										$cee = $ce->newEntity();
										$cee->content_id = $content->id;
										$cee->entity_id = $entity->id;
										$cee->research_id = $research_id;
										$cee->research_element_id = $research_element_id;
										$ce->save($cee);
									}
								}

							}
							//heashtags


							// urls
							/*

							TODO: processare gli url nel messaggio

							$urlos = array();
							if(isset($status->images->standard_resolution)){
								$urlos[] = $status->images->standard_resolution->url;
							}

							if(isset($status->images->low_resolution)){
								$urlos[] = $status->images->low_resolution->url;
							}

							if(isset($status->images->thumbnail)){
								$urlos[] = $status->images->thumbnail->url;
							}

							foreach($urlos as $urlentity){
								//
								$entity_type_id = 2;
								$text = $urlentity;

								$query = $entities->find('all', [
								    'conditions' => [
								    	'entity =' => $text,
								    	'entity_type_id =' => $entity_type_id
								    ]
								]);

								$entity = $query->first();

								if(is_null($entity)){
									$entity = $entities->newEntity();
								}

								$entity->entity_type_id = $entity_type_id;
								$entity->entity = $text;

								if($entities->save($entity)){

									$query = $ce->find('all', [
									    'conditions' => [
									    	'content_id =' => $content->id,
									    	'entity_id =' => $entity->id
									    ]
									]);

									$cee = $query->first();

									if(is_null($cee)){
										$cee = $ce->newEntity();
										$cee->content_id = $content->id;
										$cee->entity_id = $entity->id;
										$cee->research_id = $research_id;
										$cee->research_element_id = $research_element_id;
										$ce->save($cee);
									}
								}

							}
							*/
							//urls


							// media
							/*
							TODO: processare media nei messaggi
							if(isset($urlos)){
								foreach($urlos as $mediaentity){
									//
									$entity_type_id = 3;
									$text = $mediaentity;

									$query = $entities->find('all', [
									    'conditions' => [
									    	'entity =' => $text,
									    	'entity_type_id =' => $entity_type_id
									    ]
									]);

									$entity = $query->first();

									if(is_null($entity)){
										$entity = $entities->newEntity();
									}

									$entity->entity_type_id = $entity_type_id;
									$entity->entity = $text;

									if($entities->save($entity)){

										$query = $ce->find('all', [
										    'conditions' => [
										    	'content_id =' => $content->id,
										    	'entity_id =' => $entity->id
										    ]
										]);

										$cee = $query->first();

										if(is_null($cee)){
											$cee = $ce->newEntity();
											$cee->content_id = $content->id;
											$cee->entity_id = $entity->id;
											$cee->research_id = $research_id;
											$cee->research_element_id = $research_element_id;
											$ce->save($cee);
										}
									}

								}
							}
							*/
							//media


							// relations con mentions e comments


								//con mentions - inizio
									preg_match_all('/@([^\s]+)/', $content->content, $mentions);

									foreach($mentions[1] as $mention){

										$iu2 = null;
										try{
											$iu2 = Instagram::searchAccountsByUsername($mention);
										} catch (\InstagramScraper\Exception\InstagramException $e) {
										    echo 'Caught exception: ',  $e->getMessage(), "\n";
										}

										if(!is_null($iu2) && is_object($iu2)){
											print_r($iu2);

											$id_str = $iu2->id;
											$name = $iu2->fullName;
											$screen_name = $iu2->username;
											$location = "";
											$followers_count = $iu2->followedByCount;
											$friends_count = $iu2->followsCount;
											$listed_count = 0;
											$lang =  "XXX";
											$profile_url = "https://www.instagram.com/" . $screen_name . "/";;
											$profile_image_url = "";
											if(isset($iu2->profilePicUrl)){
												$profile_image_url = $iu2->profilePicUrl;	
											}
											


											$query = $subjects->find('all', [
											    'conditions' => ['profile_url =' => $profile_url]
											]);

											$subject2 = $query->first();

											if(is_null($subject2)){
												$subject2 = $subjects->newEntity();
											}

											$subject2->research_element_id = $research_element_id;
											$subject2->research_id = $research_id;
											$subject2->social_id = $id_str;
											$subject2->name = $name;
											$subject2->screen_name = $screen_name;
											$subject2->profile_url = $profile_url;

											if($subjects->save($subject2)){
												//
												$query = $relations->find('all', [
												    'conditions' => [
												    	'research_element_id =' => $research_element_id,
												    	'research_id =' => $research_id,
												    	'subject_1_id' => $subject->id,
												    	'subject_2_id' => $subject2->id
												    ]
												]);

												$relation = $query->first();

												if(is_null($relation)){
													$relation = $relations->newEntity();
													$relation->research_id = $research_id;
													$relation->research_element_id = $research_element_id;
													$relation->subject_1_id = $subject->id;
													$relation->subject_2_id = $subject2->id;
													$relation->c = 1;
												} else {
													if($isNewContent){
														$relation->c = $relation->c + 1;
													}
												}

												$relations->save($relation);
											}
										}

									}

								//con mentions - fine


								//con comments
								$comments = array();

								try{
									if(isset($content->social_id) && $content->social_id!="" ){
										//echo("[1]" . $content->social_id);
										$comments = Instagram::getMediaCommentsById($content->social_id, 10000);	
									}
								} catch (\InstagramScraper\Exception\InstagramException $e) {
								    echo 'Caught exception: ',  $e->getMessage(), "\n";
								}

									if(isset($comments) && is_array($comments)){
										foreach($comments as $comment){

											$id_str = $comment->user->id;
											$name = (is_null($comment->user->fullName)?"":$comment->user->fullName);
											$screen_name = $comment->user->username;
											$location = "";
											$followers_count = $comment->user->followedByCount;
											$friends_count = $comment->user->followsCount;
											$listed_count = 0;
											$lang =  "XXX";
											$profile_url = "https://www.instagram.com/" . $screen_name . "/";;
											$profile_image_url = "";
											if(isset($comment->user->profilePicUrl)){
												$profile_image_url = $comment->user->profilePicUrl;	
											}
											


											$query = $subjects->find('all', [
											    'conditions' => ['profile_url =' => $profile_url]
											]);

											$subject2 = $query->first();

											if(is_null($subject2)){
												$subject2 = $subjects->newEntity();
											}

											$subject2->research_element_id = $research_element_id;
											$subject2->research_id = $research_id;
											$subject2->social_id = $id_str;
											$subject2->name = $name;
											$subject2->screen_name = $screen_name;
											$subject2->profile_url = $profile_url;

											if($subjects->save($subject2)){
												//
												$query = $relations->find('all', [
												    'conditions' => [
												    	'research_element_id =' => $research_element_id,
												    	'research_id =' => $research_id,
												    	'subject_1_id' => $subject->id,
												    	'subject_2_id' => $subject2->id
												    ]
												]);

												$relation = $query->first();

												if(is_null($relation)){
													$relation = $relations->newEntity();
													$relation->research_id = $research_id;
													$relation->research_element_id = $research_element_id;
													$relation->subject_1_id = $subject->id;
													$relation->subject_2_id = $subject2->id;
													$relation->c = 1;
												} else {
													if($isNewContent){
														$relation->c = $relation->c + 1;
													}
												}

												$relations->save($relation);
											}

										}
									}
								// con comments - fine

							// relations con retweet, quote e mentions
							

						}


					}




				}

				//echo("\n\n-----------------\n\n");
			}
		}
	}
	//end process_insta_data - scraper









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


	function utf8ize($d) {
	    if (is_array($d)) 
	        foreach ($d as $k => $v) 
	            $d[$k] = utf8ize($v);

	     else if(is_object($d))
	        foreach ($d as $k => $v) 
	            $d->$k = utf8ize($v);

	     else 
	        return utf8_encode($d);

	    return $d;
	}
}
?>
