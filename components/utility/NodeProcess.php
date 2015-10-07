<?php

/*
 * Run NodeJS in background 
 */

class NodeProcess extends CComponent {

    public static function checkNode() {
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
            $pid = null;
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -accepteula -d node " . $cmd . " " . $params, $output, $input);
                $pid = $input;
            } else {
                $pid = exec("nodejs " . $cmd . " " . $params . " > /dev/null 2>&1 & echo $!;", $output, $input);
            }
            NodeProcess::addPid($pid);
            return $pid;
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
                NodeProcess::removePid($pid);
                return $input;
            } else {
                exec("kill -9 $pid");
                NodeProcess::removePid($pid);
            }
        }
    }
    
    public static function addPid($pid){
        $pids = Setting::get('nodejs.pid');
            
        if(is_null($pids)){
            $pids = [];
        }
        
        $pids[]=$pid;
        $pids = array_unique($pids);
        Setting::set('nodejs.pid', $pids);
    }
    
    public static function removePid($pid){
        $pids = Setting::get('nodejs.pid');
        if(!is_null($pids) && in_array($pid, $pids)){
            if(($key = array_search($pid, $pids)) !== false) {
                unset($pids[$key]);
            }
            $pids = array_unique($pids);
            Setting::set('nodejs.pid', $pids);
        }
    }

}
