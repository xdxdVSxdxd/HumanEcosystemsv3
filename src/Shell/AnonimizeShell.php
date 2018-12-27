<?php
namespace App\Shell;

use Cake\Console\Shell;

class AnonimizeShell extends Shell
{

    public $tasks = ['Anonimizer'];

    /**
     * Start the shell and interactive console.
     *
     * @return int|null
     */
    public function main()
    {
        $this->out("Hello: AnonimizeShell functioning correctly.");
        return 0;
    }

    public function anonimize(){
        $this->Anonimizer->anonimize();
    }


}
