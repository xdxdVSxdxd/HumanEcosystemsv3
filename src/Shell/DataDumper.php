<?php
namespace App\Shell;

use Cake\Console\Shell;

class DataDumperShell extends Shell
{

    /**
     * Start the shell and interactive console.
     *
     * @return int|null
     */
    public function main()
    {
        $this->out("Hello: DataDumper functioning correctly.");
        return 0;
    }

    public function dumpDailyKeywords($researches = -1){

        return 0;
    }

}
