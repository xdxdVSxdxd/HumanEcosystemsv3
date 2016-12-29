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

class DumperTasksTask extends Shell
{

	public function initialize()
    {
        parent::initialize();
        //$this->loadModel('Contents');
    }


    public function main()
    {

    	$this->out("DataDumper:DomperTasks:ok");

    }

    public function topUsersDay($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, count(*) as c , count(*)*(1+c.favorite_count+2*c.retweet_count) as n FROM contents c, subjects s WHERE s.id=c.subject_id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY s.id ORDER BY n DESC LIMIT 0,50');
			
		}else{
			
			$stmt = $conn->execute('SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, count(*) as c , count(*)*(1+c.favorite_count+2*c.retweet_count) as n FROM contents c, subjects s WHERE s.id=c.subject_id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND c.research_id in (' . $researches . ') GROUP BY s.id ORDER BY n DESC LIMIT 0,50');    

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->name = $row["name"];
		    $o->screen_name = $row["screen_name"];
		    $o->profile_url = $row["profile_url"];
		    $o->profile_image_url = $row["profile_image_url"];
		    $o->posts = $row["c"];
		    $o->number = $row["n"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-DailyTopUsers.json";
		unlink("webroot/DumpedData/DailyTopUsers.json");
		$filename = "webroot/DumpedData/DailyTopUsers.json";
		$this->createFile($filename, $jsoncontent);

    }


    public function topUsersWeek($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, count(*) as c , count(*)*(1+c.favorite_count+2*c.retweet_count) as n FROM contents c, subjects s WHERE s.id=c.subject_id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY s.id ORDER BY n DESC LIMIT 0,50');
			
		}else{
			
			$stmt = $conn->execute('SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, count(*) as c , count(*)*(1+c.favorite_count+2*c.retweet_count) as n FROM contents c, subjects s WHERE s.id=c.subject_id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND c.research_id in (' . $researches . ') GROUP BY s.id ORDER BY n DESC LIMIT 0,50');    

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->name = $row["name"];
		    $o->screen_name = $row["screen_name"];
		    $o->profile_url = $row["profile_url"];
		    $o->profile_image_url = $row["profile_image_url"];
		    $o->posts = $row["c"];
		    $o->number = $row["n"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-WeeklyTopUsers.json";
		unlink("webroot/DumpedData/WeeklyTopUsers.json");
		$filename = "webroot/DumpedData/WeeklyTopUsers.json";
		$this->createFile($filename, $jsoncontent);

    }

    public function topUsersMonth($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, count(*) as c , count(*)*(1+c.favorite_count+2*c.retweet_count) as n FROM contents c, subjects s WHERE s.id=c.subject_id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY s.id ORDER BY n DESC LIMIT 0,50');
			
		}else{
			
			$stmt = $conn->execute('SELECT s.name as name, s.screen_name as screen_name, s.profile_url as profile_url, s.profile_image_url as profile_image_url, count(*) as c , count(*)*(1+c.favorite_count+2*c.retweet_count) as n FROM contents c, subjects s WHERE s.id=c.subject_id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND c.research_id in (' . $researches . ') GROUP BY s.id ORDER BY n DESC LIMIT 0,50');    

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->name = $row["name"];
		    $o->screen_name = $row["screen_name"];
		    $o->profile_url = $row["profile_url"];
		    $o->profile_image_url = $row["profile_image_url"];
		    $o->posts = $row["c"];
		    $o->number = $row["n"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/MonthlyTopUsers.json");
		$filename = "webroot/DumpedData/MonthlyTopUsers.json";
		$this->createFile($filename, $jsoncontent);

    }


    public function activityDaily($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT re.content as content, HOUR(created_at) as h, count(*) as c FROM contents c , research_elements re WHERE c.research_element_id=re.id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY HOUR(created_at), c.research_element_id ORDER BY content, HOUR(created_at)');
			
		}else{
			
			$stmt = $conn->execute('SELECT re.content as content, HOUR(created_at) as h, count(*) as c FROM contents c , research_elements re WHERE c.research_id IN ( ' . $researches . ' ) AND c.research_element_id=re.id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY HOUR(created_at), c.research_element_id ORDER BY content, HOUR(created_at)');    

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->research = $row["content"];
		    $o->hour = $row["h"];
		    $o->count = $row["c"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/DailyActivity.json");
		$filename = "webroot/DumpedData/DailyActivity.json";
		$this->createFile($filename, $jsoncontent);

    }

    public function activityWeekly($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT re.content as content, HOUR(created_at) as h, count(*) as c FROM contents c , research_elements re WHERE c.research_element_id=re.id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY HOUR(created_at), c.research_element_id ORDER BY content, HOUR(created_at)');
			
		}else{
			
			$stmt = $conn->execute('SELECT re.content as content, HOUR(created_at) as h, count(*) as c FROM contents c , research_elements re WHERE c.research_id IN ( ' . $researches . ' ) AND c.research_element_id=re.id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY HOUR(created_at), c.research_element_id ORDER BY content, HOUR(created_at)');    

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->research = $row["content"];
		    $o->hour = $row["h"];
		    $o->count = $row["c"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/WeeklyActivity.json");
		$filename = "webroot/DumpedData/WeeklyActivity.json";
		$this->createFile($filename, $jsoncontent);

    }


    public function activityMonthly($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT re.content as content, HOUR(created_at) as h, count(*) as c FROM contents c , research_elements re WHERE c.research_element_id=re.id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY HOUR(created_at), c.research_element_id ORDER BY content, HOUR(created_at)');
			
		}else{
			
			$stmt = $conn->execute('SELECT re.content as content, HOUR(created_at) as h, count(*) as c FROM contents c , research_elements re WHERE c.research_id IN ( ' . $researches . ') AND c.research_element_id=re.id AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY HOUR(created_at), c.research_element_id ORDER BY content, HOUR(created_at)');    

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->research = $row["content"];
		    $o->hour = $row["h"];
		    $o->count = $row["c"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/MonthlyActivity.json");
		$filename = "webroot/DumpedData/MonthlyActivity.json";
		$this->createFile($filename, $jsoncontent);

    }


    public function sentimentDaily($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT comfort,energy,count(*) as c FROM contents c WHERE c.created_at>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY comfort,energy');
			
		}else{
			
			$stmt = $conn->execute('SELECT comfort,energy,count(*) as c FROM contents c WHERE research_id IN ( ' . $researches . ' ) AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 1 DAY)) GROUP BY comfort,energy');

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->comfort = $row["comfort"];
		    $o->energy = $row["energy"];
		    $o->count = $row["c"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/DailySentiment.json");
		$filename = "webroot/DumpedData/DailySentiment.json";
		$this->createFile($filename, $jsoncontent);

    }

    public function sentimentWeekly($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT comfort,energy,count(*) as c FROM contents c WHERE c.created_at>=DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY comfort,energy');
			
		}else{
			
			$stmt = $conn->execute('SELECT comfort,energy,count(*) as c FROM contents c WHERE research_id IN ( ' . $researches . ' ) AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 7 DAY)) GROUP BY comfort,energy');

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->comfort = $row["comfort"];
		    $o->energy = $row["energy"];
		    $o->count = $row["c"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/WeeklySentiment.json");
		$filename = "webroot/DumpedData/WeeklySentiment.json";
		$this->createFile($filename, $jsoncontent);

    }


    public function sentimentMonthly($researches = -1){
    	$results = array();
		
		$researcharray = explode(",", $researches  );

		$conn = ConnectionManager::get('default');

		$stmt = null;

		if( $researches==-1 ){

			$stmt = $conn->execute('SELECT comfort,energy,count(*) as c FROM contents c WHERE c.created_at>=DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY comfort,energy');
			
		}else{
			
			$stmt = $conn->execute('SELECT comfort,energy,count(*) as c FROM contents c WHERE research_id IN ( ' . $researches . ' ) AND c.created_at>=DATE_SUB(CURDATE(), INTERVAL 30 DAY)) GROUP BY comfort,energy');

		}

		$rows = $stmt->fetchAll('assoc');
		foreach ($rows as $row) {
		    $o = new \stdClass();
		    $o->comfort = $row["comfort"];
		    $o->energy = $row["energy"];
		    $o->count = $row["c"];
		    $results[] = $o;
		}


		$jsoncontent = json_encode( $results );
		$now = Time::now();
		//$filename = "webroot/DumpedData/" . $now->i18nFormat('yyyy-MM-dd-HH-mm-ss') . "-MonthlyTopUsers.json";
		unlink("webroot/DumpedData/MonthlySentiment.json");
		$filename = "webroot/DumpedData/MonthlySentiment.json";
		$this->createFile($filename, $jsoncontent);

    }

}
?>