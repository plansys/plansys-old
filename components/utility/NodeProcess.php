<?php

/*
 * Run NodeJS in background 
 */

class NodeProcess extends CComponent {

    public static function start($jsfile) {
        $cmd = realpath(Yii::getPathOfAlias('webroot') . $jsfile);

        if ($cmd) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -d node " . $cmd, $output, $input);
                return $input;
            } else {
                exec("nodejs " . $cmd . " > /dev/null &", $output, $input);
                var_dump($input);
                die();
            }
        }
    }

    public function isRunning($pid = 0) {
        return true;
    }

    public function stop($pid) {
        if ($pid) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pskill.exe " . $pid, $output, $input);
                return $input;
            } else {
                exec("nodejs " . $cmd . " > /dev/null &");
            }
        }
    }

}
