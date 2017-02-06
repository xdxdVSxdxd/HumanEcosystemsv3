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

require_once ('vendor/emotions/stopwords.php');

class FacebookPagesHarvestTask extends Shell
{

	public function initialize()
    {
        parent::initialize();
        //$this->loadModel('Contents');
    }


    public function main()
    {

    	$this->out("FacebookPagesHarvestTask:ok");

    }

    public function harvest(){

    	

    	$results = array();

    	$stopwords = new \StopWords();

    	
		
		$conn = ConnectionManager::get('default');

		

		$stmt = $conn->execute('SELECT * FROM smiley_emotions');
		$sm = $stmt->fetchAll('assoc');
		$smileys = array();
		foreach($sm as $s){
			$smiley = new \stdClass();
			$smiley->smiley = $s["smiley"];
			$smiley->emotion_id = $s["emotion_id"];
			$smileys[] = $smiley;
 		}

 		

 		$stmt = $conn->execute('SELECT * FROM word_emotions');
		$wo = $stmt->fetchAll('assoc');
		$wordsem = array();
		foreach($wo as $w){
			$word = new \stdClass();
			$word->word = $w["word"];
			$word->emotion_id = $w["emotion_id"];
			$wordsem[] = $word;
 		}

 		

 		$stmt = $conn->execute('SELECT * FROM emotion_types');
		$et = $stmt->fetchAll('assoc');
 		$etypes = array();
 		foreach($et as $e){
 			$ett = new \stdClass();
 			$ett->id = $e["id"];
 			$ett->comfort = $e["comfort"];
 			$ett->energy = $e["energy"];
 			$etypes[] = $ett;
 		}

		

		$stmt = $conn->execute('SELECT * FROM research_elements re WHERE re.research_element_type_id=4 AND ACTIVE=1 ORDER BY updated_last ASC LIMIT 0,1');
		
		
		$rows = $stmt->fetchAll('assoc');
		if(count($rows)>0){
			$row = $rows[0];

			$stmt2 = $conn->execute('UPDATE research_elements SET updated_last=NOW() WHERE id=' . $row["id"]);

			$stmt3 = $conn->execute('SELECT * FROM researches WHERE id=' . $row["research_id"] . " LIMIT 0,1");
			$rows3 = $stmt3->fetchAll('assoc');
			if(count($rows3)>0){
				$row3 = $rows3[0];

				$ids= explode(",", $row["content"]);
				$fbAppId = $row3["fb_app_id"];
				$fbAppSecret = $row3["fb_app_secret"];

				$id_research_element = $row["id"];
				$id_research = $row["research_id"];

				
				if(isset($fbAppId) && isset($fbAppSecret) && $fbAppId!="" && $fbAppSecret!=""){
					foreach ($ids as $id) {

						//echo($id . "-->\n\n\n" );

						exec('python FB/get_fb_posts_fb_page.py ' . $id . ' ' . $fbAppId . ' ' . $fbAppSecret, $output);
						$fname = $id . "_facebook_statuses.csv";

						

						$handle = fopen($fname, "r");
						$isfirst = true;
						if ($handle) {
						    while (!feof($handle)) {
						        $buffer = fgets($handle, 8192);
						        $parts = explode(",", $buffer);

						        //print_r($parts);

						        if(!$isfirst && count($parts)==15){

						        	

						        	$id_subject = -1;
						        	$id_str = str_replace("'", " ", $id);
									$name = str_replace("'", " ", $id);
									$screen_name = str_replace("'", " ", $id );
									$location = "";
									$followers_count = $parts[9] + $parts[8] + $parts[7] + $parts[6];
									$friends_count = 0;
									$listed_count = 0;
									$lang =  "XXX";
									$profile_url = "https://www.facebook.com/search/top/?q=" . str_replace("'", " ", $id );
									$profile_image_url = "";

									
									$q1 = "SELECT * FROM subjects WHERE profile_url='" . $profile_url . "' LIMIT 0,1";
									$s1 = $conn->execute($q1);
									$row1 = $s1->fetchAll('assoc');
									if(count($row1)>0){
										
										$r1 = $row1[0];
										// aggiornare
										$id_subject = $r1["id"];
										$followers_count = $followers_count + $r1["followers_count"];
										$q2 = "UPDATE subjects SET followers_count=" . $followers_count . " WHERE id=" . $id_subject;
										$s2 = $conn->execute($q2);
										
									} else {
										
										// inserire e prendere ID
										$q2 = "INSERT INTO subjects(research_element_id,research_id,name,social_id,screen_name,location,followers_count,friends_count,listed_count,language,profile_url,profile_image_url) VALUES( " . $id_research_element . "," . $id_research . ",'" . $name . "','" . $id_str . "','" . $screen_name . "','" . $location . "'," . $followers_count . "," . $friends_count . "," . $listed_count . ",'" . $lang . "','" . $profile_url . "','" . $profile_image_url . "' )";
										$s2 = $conn->execute($q2);
										$id_subject = $s2->lastInsertId('subjects');
										
									}

									$id_content = -1;
									$link = "https://www.facebook.com/" . $parts[0];
									$text = str_replace("'", " ", $parts[1]);
									if(isset($parts[2]) && $parts[2]!=""){
										$text = str_replace("'", " ", $parts[2]) . " – " . $text;
									}
									$created_at = $parts[5]; //new Time( $parts[6] );
									$social_id = str_replace("'", " ", $parts[0]);
									$language = "XXX";
									$favorite_count = $parts[9];
									$retweet_count = $parts[8];

									$lat = -999;
									$lng = -999;

									
									$q1 = "SELECT * FROM contents WHERE link='" . $link . "' LIMIT 0,1";
									$s1 = $conn->execute($q1);
									$row1 = $s1->fetchAll('assoc');
									$isNewContent = false;
									if(count($row1)>0){
										$r1 = $row1[0];
										// aggiornare
										$id_content = $r1["id"];
										$q2 = "UPDATE contents SET favorite_count=" . $favorite_count . ", retweet_count=" . $retweet_count . " WHERE id=" . $id_content;
										$s2 = $conn->execute($q2);
										$isNewContent = false;
										
									} else {
										// inserire e prendere ID
										$q2 = "INSERT INTO contents(research_element_id,research_id,subject_id,link,content,created_at,social_id,language,favorite_count,retweet_count,lat,lng) VALUES( " . $id_research_element . "," . $id_research . "," . $id_subject . ",'" . $link . "','" . $text . "','" . $created_at . "','" . $social_id . "','" . $language . "'," . $favorite_count . "," . $retweet_count . "," . $lat . "," . $lng . ")";
										$s2 = $conn->execute($q2);
										$id_content = $s2->lastInsertId('contents');
										$isNewContent = true;
										
									}

									if($isNewContent){

										

										//process emotions
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

											$q3 = "INSERT INTO emotions(research_id,research_element_id,content_id,emotion_type_id,c) VALUES (" . $id_research . "," . $id_research_element . "," . $id_content . "," . $emo . "," . $value . ")";

											$s3 = $conn->execute($q3);
											
											
										}

										foreach ($results_words as $key => $value) {

											

											$key = str_replace("emo-", " ", $key);
											$emo = intval($key);

											$q3 = "INSERT INTO emotions(research_id,research_element_id,content_id,emotion_type_id,c) VALUES (" . $id_research . "," . $id_research_element . "," . $id_content . "," . $emo . "," . $value . ")";

											$s3 = $conn->execute($q3);
											
										}

										$q3 = "UPDATE contents SET comfort=" . $comfort_tot . ",energy=" . $energy_tot . " WHERE id=" . $id_content;
										$s3 = $conn->execute($q3);
										//process emotions

										

									}

									if(isset($parts[5]) && $parts[5]!=""){
										
										$urlentity = $parts[5];
										$id_entity = -1;
										$entity_type_id = 2;
										$text = str_replace("'", "\'", $urlentity);

										$q4 = "SELECT * FROM entities WHERE entity='" . $text . "' AND entity_type_id=" . $entity_type_id;
										$s4 = $conn->execute($q4);
										$row4 = $s4->fetchAll('assoc');
										if(count($row4)>0){
											$id_entity = $row4[0]["id"];
										} else {
											$q5 = "INSERT INTO entities(entity_type_id,entity) VALUES (" . $entity_type_id . ",'" . $text . "')";
											$s5 = $conn->execute($q5);
											$id_entity = $s5->lastInsertId('entities');
										}
										$q6 = "INSERT INTO contents_entities(content_id,research_id,research_element_id,entity_id) VALUES (" . $id_content . "," . $id_research . "," . $id_research_element . "," . $id_entity . ")";
										$s6 = $conn->execute($q6);
										
										
									}

						        } else { $isfirst = false; }
								
						        

						    }
						    fclose($handle);
						}

						unlink($fname);


					}
				}

			}

		}

		//$jsoncontent = json_encode( $results );
		//$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-DailyTopUsers.json";
		//unlink("webroot/DumpedData/DailyTopUsers.json");
		//$filename = "webroot/DumpedData/DailyTopUsers.json";
		//$this->createFile($filename, $jsoncontent);

    }
    
}
?>