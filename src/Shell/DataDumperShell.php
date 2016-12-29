<?php
namespace App\Shell;

use Cake\Console\Shell;

class DataDumperShell extends Shell
{

    public $tasks = ['DailyKeywords','DailyKeywordRelations','DumperTasks'];

    /**
     * Start the shell and interactive console.
     *
     * @return int|null
     */
    public function main()
    {
        $this->out("Hello: DataDumper functioning correctly.");
        $this->DailyKeywords->dump();
        $this->DailyKeywordRelations->dump();
        return 0;
    }

    public function daily(){
        $this->DumperTasks->topUsersDay();
        $this->DumperTasks->activityDaily();
        $this->DumperTasks->sentimentDaily();
    }

    public function weekly(){
        $this->DumperTasks->topUsersWeek();
        $this->DumperTasks->activityWeekly();
        $this->DumperTasks->sentimentWeekly();
    }

    public function monthly(){
        $this->DumperTasks->topUsersMonth();
        $this->DumperTasks->activityMonthly();
        $this->DumperTasks->sentimentMonthly();
    }

}
