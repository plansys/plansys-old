<?php

/*
 * Run NodeJS in background 
 */

class NodeProcess extends CComponent {

    public static function start($jsfile) {
        $cmd = realpath(Yii::getPathOfAlias('webroot') . $jsfile);
        
        if (is_file($cmd)) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -d node " . $cmd, $output, $input);
                return $input;
            } else {
                $pid = exec("nodejs " . $cmd . " > /dev/null 2>&1 & echo $!;", $output, $input);
                return $pid;
            }
        } else {
            throw new CException("File Not Found");
        }
    }

    public function isRunning($pid = 0) {
        if (is_numeric($pid)) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pslist.exe " . $pid, $output, $input);
                return $input == 0;
            } else {
                return (file_exists("/proc/$pid"));
            }
        }
    }

    public function stop($pid) {
        if ($pid) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pskill.exe " . $pid, $output, $input);
                return $input;
            } else {
                exec("kill -9 $pid");
            }
        }
    }

}
