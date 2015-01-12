<?php

/*
 * Run NodeJS in background 
 */

class NodeProcess extends CComponent {

    public static function start($jsfile) {
        $cmd = realpath(Yii::getPathOfAlias('webroot') . $jsfile);

        if ($cmd) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -i -d node " . $cmd);
            } else {
                exec("nodejs " . $cmd . " > /dev/null &");
            }
        }
    }

    public function isRunning($id = 0) {
        return true;
    }

    public function stop($id = 0) {
        
    }

}
