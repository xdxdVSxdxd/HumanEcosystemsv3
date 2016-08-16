<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Table\ResearcheElementsTable;
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


class ResearchElementsController extends AppController
{


	public function beforeFilter(Event $event){
		parent::beforeFilter($event);
		$this->Auth->allow( [ 'controller' => 'research-elements' , 'action' => 'process' ] );
	}


	function process($id){
		//$researchelements = TableRegistry::get('ResearchElements');
		$researchelement = $this;

		$researchelement->process( $researchelement->id );

		/*
		if($researchelement->research_element_type_id==1){
			$researchelement->twitterkeyword($id);
			$researchelement->instakeyword($id);
		} else if($researchelement->research_element_type_id==2){
			$researchelement->twittermentions($id);
			$researchelement->instamentions($id);
		} else if($researchelement->research_element_type_id==3){
			$researchelement->twitterhashtags($id);
			$researchelement->instahashtags($id);
		} else if($researchelement->research_element_type_id==4){
			$researchelement->fbpage($id);
		} else if($researchelement->research_element_type_id==5){
			$researchelement->twitteruser($id);
		} else if($researchelement->research_element_type_id==6){
			$researchelement->instauser($id);
		}
		*/

		$this->autoRender = false;

	}

	

}
?>