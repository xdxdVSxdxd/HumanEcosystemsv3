<?php
namespace App\Shell;

use Cake\Console\Shell;

class FacebookShell extends Shell
{

    public $tasks = ['FacebookGroupsHarvest','FacebookPagesHarvest'];

    /**
     * Start the shell and interactive console.
     *
     * @return int|null
     */
    public function main()
    {
        $this->out("Hello: FacebookShell functioning correctly.");
        return 0;
    }

    public function groups(){
        $this->FacebookGroupsHarvest->harvest();
    }

    public function pages(){
        $this->FacebookPagesHarvest->harvest();
    }

}
