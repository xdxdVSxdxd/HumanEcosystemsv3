<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Table\ResearchesTable;
use App\Model\Table\ResearcheElementTypesTable;
use App\Model\Table\ResearcheElementsTable;
use App\Model\Table\ContentsEntities;
use App\Model\Entity\Research;
use App\Model\Entity\ResearchElement;
use App\Model\Table\ContentsTable;
use App\Model\Entity\Content;
use App\Model\Table\SubjectsTable;
use App\Model\Entity\Subject;
use App\Model\Table\RelationsTable;
use App\Model\Entity\Relation;
use App\Model\Table\EmotionsTable;
use App\Model\Entity\Emotion;
use Cake\Routing\Router;
use Cake\I18n\Time;

use InstagramScraper\Instagram;

class ResearchesController extends AppController
{

	public function beforeFilter(Event $event){
		parent::beforeFilter($event);
		$this->Auth->allow( [ 'process', 'processsingle' ] );
	}

	public function processsingle(){
		$researches = TableRegistry::get('Researches');
		$researchelements = TableRegistry::get('ResearchElements');

		echo("research ID:" . $this->request->query('id') );

		$querye = $researchelements->find('all', [
		    'conditions' => ['research_id = ' => $this->request->query('id')]
		]);

		foreach($querye as $re){
			$reid = $re->id;

			echo("Starting Research Element: " . $re->id  . "\n");


			$re->process($reid);

		}

		$this->autoRender = false;

	}

	public function process(){
		$researches = TableRegistry::get('Researches');
		$researchelements = TableRegistry::get('ResearchElements');

		$query = $researches->find('all', [
		    'order' => ['last_updated' => 'ASC']
		]);

		$query->matching('ResearchElements', function ($q) {
		    return $q->where(['ResearchElements.active' => 1, 'ResearchElements.research_element_type_id !=' => 4, 'ResearchElements.research_element_type_id !=' => 7]);
		});

		$research = $query->first();

		//echo("Starting Research: " . $research->id  . "\n");

		if(isset($research) ){
			$research->last_updated = new Time( 'now' );

			$researches->save($research);

			$querye = $researchelements->find('all', [
			    'conditions' => ['research_id = ' => $research->id]
			]);

			foreach($querye as $re){
				$reid = $re->id;

				//echo("Starting Research Element: " . $re->id  . "\n");


				$re->process($reid);

			}
		}


		$this->autoRender = false;

	}

	public function test(){

		$medias = Instagram::getMedias('base_milano', 30);
		print_r($medias);

		$this->autoRender = false;
	}

	public function deleteresearchelement(){
		$res = array();
		$res["status"] = "fail";

		if(isset($_REQUEST["id"])){

			$id = $_REQUEST["id"];

			$contententities = TableRegistry::get('ContentsEntities');
			$contents = TableRegistry::get('Contents');
			$emotions = TableRegistry::get('Emotions');
			$subjects = TableRegistry::get('Subjects');
			$relations = TableRegistry::get('Relations');
			$researchelements = TableRegistry::get('ResearchElements');

			$contententities->deleteAll(['research_element_id' => $id ]);
			$emotions->deleteAll(['research_element_id' => $id ]);
			$contents->deleteAll(['research_element_id' => $id ]);
			$relations->deleteAll(['research_element_id' => $id ]);
			$subjects->deleteAll(['research_element_id' => $id ]);
			$researchelements->deleteAll(['id' => $id ]);

			$res["status"] = "success";

		}

		echo(json_encode(  [$res] ));

		$this->autoRender = false;
		
	}

	public function delete( $id ){
		$researches = TableRegistry::get('Researches');
		$contententities = TableRegistry::get('ContentsEntities');
		$contents = TableRegistry::get('Contents');
		$emotions = TableRegistry::get('Emotions');
		$subjects = TableRegistry::get('Subjects');
		$relations = TableRegistry::get('Relations');
		$researchelements = TableRegistry::get('ResearchElements');


		$contententities->deleteAll(['research_id' => $id ]);
		$emotions->deleteAll(['research_id' => $id ]);
		$contents->deleteAll(['research_id' => $id ]);
		$relations->deleteAll(['research_id' => $id ]);
		$subjects->deleteAll(['research_id' => $id ]);
		$researchelements->deleteAll(['research_id' => $id ]);

		$rese = $researches->get($id);
		$resu = $researches->delete($rese);

		return $this->redirect(
            array('controller' => 'researches', 'action' => 'listresearches')
        );

	}

	public function toggleresearchelementactive(){

		$re_id = $_REQUEST["id"];
		$state = $_REQUEST["state"];

		$ree = TableRegistry::get('ResearchElements');
		$re = $ree->get($re_id);

		$res = "fail";

		if(!is_null($re)){
			$re->active = $state;
			$ree->save($re);
			$res = "success";
		}

		echo(json_encode(  [$res] ));

		$this->autoRender = false;
	}

	public function updateresearchconfig( $id ){

		/*
			"twconsumerkey": twconsumerkey,
			"twconsumersecret": twconsumersecret,
			"twtoken": twtoken,
			"twtokensecret": twtokensecret,
			"twbearertoken": twbearertoken,
			"instaclientid": instaclientid,
			"instaapptoken": instaapptoken,
			"fbappid": fbappid,
			"fbapsecret": fbapsecret
		*/

		$researches = TableRegistry::get('Researches');
		
		$row = $researches->get($id);

		$row->twitter_consumer_key = $_REQUEST["twconsumerkey"];
		$row->twitter_consumer_secret = $_REQUEST["twconsumersecret"];
		$row->twitter_token = $_REQUEST["twtoken"];
		$row->twitter_token_secret = $_REQUEST["twtokensecret"];
		$row->twitter_bearer_token = $_REQUEST["twbearertoken"];
		$row->insta_client_id = $_REQUEST["instaclientid"];
		$row->insta_token = $_REQUEST["instaapptoken"];
		$row->fb_app_id = $_REQUEST["fbappid"];
		$row->fb_app_secret = $_REQUEST["fbapsecret"];



		if($researches->save($row)){
			echo( "success" );
		} else {
			echo("fail");
		}

		$this->render(false);

	}

	public function addresearchelement()
    {

    	$res = array();
    	$res["status"] = "fail";

    	/*
			control parameters:
					"ptypeID": ptypeID,
					"pvalue": pvalue,
					"plat": plat,
					"plng": plng,
					"planguage": planguage,
					'research_id': <?php  echo( $research_id ); ?>,
					'user_id': <?php  echo( $user_id ); ?>
    	*/

    	if(  
    		isset( $_REQUEST["ptypeID"]) &&
    		isset( $_REQUEST["pvalue"]) &&
    		isset( $_REQUEST["plat"]) &&
    		isset( $_REQUEST["plng"]) &&
    		isset( $_REQUEST["planguage"]) &&
    		isset( $_REQUEST["research_id"]) &&
    		isset( $_REQUEST["user_id"]) 
    	){

    		$ret = TableRegistry::get('ResearchElements');
    		$newRe = $ret->newEntity();
    		$newRe->research_element_type_id = $_REQUEST["ptypeID"];
    		$newRe->research_id = $_REQUEST["research_id"];
    		$newRe->content = $_REQUEST["pvalue"];
    		$newRe->lat = $_REQUEST["plat"];
    		$newRe->lng = $_REQUEST["plng"];
    		$newRe->language = $_REQUEST["planguage"];

    		if ($ret->save($newRe)) {
    			$res["status"] = "success";
    			$res["id"] = $newRe->id;
    			$res["research_element_type_id"] = $newRe->research_element_type_id;
    			$res["research_id"] = $newRe->research_id;
    			$res["content"] = $newRe->content;
    			$res["lat"] = $newRe->lat;
    			$res["lng"] = $newRe->lng;
    			$res["language"] = $newRe->language;
    			$res["active"] = $newRe->active;
    		}
    		
    	}
    	
    	echo(json_encode(  [$res] ));

		$this->autoRender = false;

    }

	public function view( $id ){

		$replaceunderscore = "IIoOiOiOOiO9987";
		$replacebar = "mmMMmmMmM55645M";


		$researches = TableRegistry::get('Researches');
		$query = $researches->find('all', [
		    'conditions' => ['id =' => $id],
		    'contain' => ['ResearchElements']
		]);

		$row = $query->first();

		$this->set('research_id',$row->id);
		$this->set('research_name',$row->name);

		$this->set('twitter_consumer_key',$row->twitter_consumer_key);
		$this->set('twitter_consumer_secret',$row->twitter_consumer_secret);
		$this->set('twitter_token',$row->twitter_token);
		$this->set('twitter_token_secret',$row->twitter_token_secret);
		$this->set('twitter_bearer_token',$row->twitter_bearer_token);
		$this->set('insta_client_id',$row->insta_client_id);
		$this->set('insta_token',$row->insta_token);
		$this->set('fb_app_id',$row->fb_app_id);
		$this->set('fb_app_secret',$row->fb_app_secret);

		$this->set("rediruri",Router::url(['controller' => 'Utilities', 'action' => 'instaurl' ],true));

		$contents = TableRegistry::get('Contents');
		$tot_contents = $contents->find(
			'all', 
			array(
				'conditions' => array(
					'research_id =' => $id
				)
			) 
		);
		$this->set('tot_contents',$tot_contents->count());


		$subjects = TableRegistry::get('Subjects');
		$tot_subjects = $subjects->find(
			'all', 
			array(
				'conditions' => array(
					'research_id =' => $id
				)
			) 
		);
		$this->set('tot_subjects',$tot_subjects->count());

		$relations = TableRegistry::get('Relations');
		$tot_relations = $relations->find(
			'all', 
			array(
				'conditions' => array(
					'research_id =' => $id
				)
			) 
		);
		$this->set('tot_relations',$tot_relations->count());


		$emotions = TableRegistry::get('Emotions');
		$tot_emotions = $emotions->find(
			'all', 
			array(
				'conditions' => array(
					'research_id =' => $id
				)
			) 
		);
		$this->set('tot_emotions',$tot_emotions->count());


		$re = TableRegistry::get('ResearchElements');
		$resele = $re->find(
			'all', 
			array(
				'conditions' => array(
					'research_id =' => $id
				)
			) 
		);
		$res = array();
		foreach($resele as $r){
			$res[] = $r;
		}
		$this->set('researchelements',$res);

		$rtt = TableRegistry::get('ResearchElementTypes');

		$queryrt = $rtt->find('all');
		$this->set('user_id',$this->Auth->user('id'));
		$this->set('replacebar',$replacebar);
		$this->set('replaceunderscore',$replaceunderscore);
		$this->set('research_element_types',$queryrt);

	}

	/*
	public function edit( $id ){
		$researches = TableRegistry::get('Researches');

		$query = $researches->find('all', [
		    'conditions' => ['id =' => $id],
		    'contain' => ['ResearchElements']
		]);

		$row = $query->first();

		$this->set('research_id',$row->id);
		$this->set('research_name',$row->name);

		$reselements = "";

		foreach($row->research_elements as $re){
			$rr = $re->id . "|" . $re->research_element_type_id . "|" . $re->content;
			$reselements = $reselements . "_" . $rr;
		}

		$this->set('research_elements',$reselements);

		
		
	}
	*/

	public function listresearches()
    {

    	$researches = TableRegistry::get('Researches');

    	$query = $researches->find('all', [
		    'conditions' => ['user_id =' => $this->Auth->user('id')]
		]);

    	$reslist = array();
		foreach($query as $r){

			$rr = array();
			$rr["id"] = $r->id;
			$rr["name"] = $r->name;
			$reslist[] = $rr;

		}

		$this->set('user_id',$this->Auth->user('id'));
		$this->set('user_name',$this->Auth->user('username'));
		$this->set('researchlist',$reslist);

    }

    public function add(){

    	$replaceunderscore = "IIoOiOiOOiO9987";
		$replacebar = "mmMMmmMmM55645M";

    	$rt = TableRegistry::get('ResearchElementTypes');

    	//print_r($this->request);
    	$newresearchname = $this->request->data("research-name");

    	
		$newresearchelements = explode( "_" , $this->request->data("parameters-list") );

    	if( isset($newresearchname) && $newresearchname!=""){
    		
    		$ret = TableRegistry::get('Researches');
    		$newResearch = $ret->newEntity();

    		$newResearch->user_id = $this->Auth->user('id');
    		$newResearch->name = $newresearchname;

    		if ($ret->save($newResearch)) {
			    // The $article entity contains the id now

    			//print_r($newresearchelements);

    			$this->Flash->error(__('Saved research!'));

			    $rid = $newResearch->id;
			    $reet = TableRegistry::get('ResearchElements');

			    foreach($newresearchelements as $re){


			    	$re = str_replace($replaceunderscore, "_", $re);

			    	$parts = explode("|",$re);
			    	if(isset($parts) && count($parts)>=2){
			    		$newResearchElement = $reet->newEntity();
			    		$newResearchElement->research_element_type_id = str_replace($replacebar, "|", $parts[0]);
			    		$newResearchElement->research_id = $rid;
			    		$newResearchElement->content = str_replace($replacebar, "|", $parts[1]);
			    		$newResearchElement->lat = str_replace($replacebar, "|", $parts[2]);
			    		$newResearchElement->lng = str_replace($replacebar, "|", $parts[3]);
			    		$newResearchElement->language = str_replace($replacebar, "|", $parts[4]);

			    		if ($reet->save($newResearchElement)) {
			    			//salvato
			    			$this->Flash->error(__('Saved research element!'));
			    		}
			    	}
			    }

			    return $this->redirect(
		            array('controller' => 'researches', 'action' => 'listresearches')
		        );
			}
    	}


    	
    	$query = $rt->find('all');
    	$this->set('user_id',$this->Auth->user('id'));
		$this->set('research_element_types',$query);

    }

}

?>