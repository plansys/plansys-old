<?php

/*
 * Run NodeJS in background 
 */

class NodeProcess extends CComponent {

    private static function checkNode() {
        if (substr(php_uname(), 0, 7) == "Windows") {
            exec("plansys\commands\shell\psexec.exe -accepteula -d node -v", $output, $input);

            if ($input < 100) {
                throw new CException("NodeJS is not installed. Command `node` not found");
            }
        } else {

            $returnVal = shell_exec("which node");
            if (empty($returnVal)) {
                throw new CException("NodeJS is not installed. Command `node` not found");
            }
        }
    }

    public static function start($jsfile, $params = "") {
        $cmd = realpath(Yii::getPathOfAlias('webroot') . '/' . trim($jsfile, '/'));

        if (is_file($cmd)) {
            NodeProcess::checkNode();

            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -accepteula -d node " . $cmd . " " . $params, $output, $input);
                return $input;
            } else {
                $pid = exec("nodejs " . $cmd . " " . $params . " > /dev/null 2>&1 & echo $!;", $output, $input);
                return $pid;
            }
        } else {
            throw new CException("File Not Found " . $cmd);
        }
    }

    public static function isRunning($pid = 0) {
        if (is_numeric($pid)) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pslist.exe -accepteula " . $pid, $output, $input);
                return $input == 0;
            } else {
                return (file_exists("/proc/$pid"));
            }
        }
    }

    public static function stop($pid) {
        if ($pid) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pskill.exe -accepteula " . $pid, $output, $input);
                return $input;
            } else {
                exec("kill -9 $pid");
            }
        }
    }

}
