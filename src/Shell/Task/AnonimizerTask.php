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

class AnonimizerTask extends Shell
{

	public function initialize()
    {
        parent::initialize();
        //$this->loadModel('Contents');
    }


    public function main()
    {

    	$this->out("Anonimizer:ok");

    }

    public function anonimize(){

    			
		$conn = ConnectionManager::get('default');

		
		$stmt = $conn->execute('SELECT * FROM contents');
		$sm = $stmt->fetchAll('assoc');
		$smileys = array();
		foreach($sm as $s){
			$link = $s["link"];
			$content = $s["content"];
			$social_id = $s["social_id"];
			$id = $s["id"];

			$content = preg_replace('/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '@MENTION', $content);
			$link = hash("sha512", $link);
			$social_id = hash("sha512", $social_id);

			$stmt2 = $conn->execute('UPDATE contents SET link="' .  str_replace('"', " ", $link)  . '" , content="' .  str_replace('"', " ", $content)  . '" , social_id="' .  str_replace('"', " ", $social_id)  . '" WHERE id=' . $id);
 		}



 		$stmt = $conn->execute('SELECT * FROM subjects');
		$sm = $stmt->fetchAll('assoc');
		$smileys = array();
		foreach($sm as $s){
			$name = $s["name"];
			$screen_name = $s["screen_name"];
			$social_id = $s["social_id"];
			$profile_url = $s["profile_url"];
			$id = $s["id"];

			$name = hash("sha512", $name);
			$screen_name = hash("sha512", $screen_name);
			$social_id = hash("sha512", $social_id);
			$profile_url = hash("sha512", $profile_url);

			$stmt2 = $conn->execute('UPDATE subjects SET name="' .  str_replace('"', " ", $name)  . '" , screen_name="' .  str_replace('"', " ", $screen_name)  . '" , social_id="' .  str_replace('"', " ", $social_id)  . '" , profile_url="' .  str_replace('"', " ", $profile_url)  . '" WHERE id=' . $id);
 		}




    }
    
}
?>