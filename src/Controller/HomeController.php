<?php
namespace App\Controller;

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