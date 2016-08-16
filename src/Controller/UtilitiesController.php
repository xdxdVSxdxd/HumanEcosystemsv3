<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class UtilitiesController extends AppController
{
	public function beforeFilter(Event $event){
		parent::beforeFilter($event);
		$this->Auth->allow( [ 'controller' => 'utilities' , 'action' => 'getTwitterBearerToken' ] );
        $this->Auth->allow( [ 'controller' => 'utilities' , 'action' => 'instaurl' ] );
	}

	public function getTwitterBearerToken()
    {

    }

    public function instaurl()
    {

    	
    }
}
?>