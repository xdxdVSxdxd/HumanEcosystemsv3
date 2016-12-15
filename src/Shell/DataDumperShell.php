<?php
namespace App\Shell;

use Cake\Console\Shell;

class DataDumperShell extends Shell
{

    public $tasks = ['DailyKeywords','DailyKeywordRelations'];

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

}
