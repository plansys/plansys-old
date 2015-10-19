<?php

class ProcessHelper extends CComponent {

    public static function listAllCmdForGridView() {
        $prcs = Setting::get("process");
        $data = [];
        foreach ($prcs as $id => $prc) {
            $data[] = [
                'id' => $id,
                'file' => $prc['file'],
                'name' => $prc['name'],
                'command' => $prc['command'],
                'period' => $prc['period'],
                'periodType' => $prc['periodType'],
                'lastRun' => $prc['lastRun'],
                'isStarted' => $prc['isStarted'],
                'runOnce' => $prc['runOnce']
            ];
        }

        return $data;
    }

    public static function listCmdForMenuTree() {
        $list    = ['' => '-- Choose Command ---'];
        $devMode = Setting::get('app.mode') === "plansys";

        /*
        ** Fetching all command files inside app.commands
        **/
        $cmdsDir = Yii::getPathOfAlias("app.commands") . DIRECTORY_SEPARATOR;
        $cmds    = self::listCmdInDir('app', $cmdsDir);
        foreach ($cmds as $cmd) {
            $shortUrl                   = Helper::explodeLast(".", $cmd['url']);
            $list['App'][$cmd['class']] = $shortUrl;
        }

        /*
        ** Fetching all command files inside app.modules
        **/

        $dir   = Yii::getPathOfAlias("app.modules") . DIRECTORY_SEPARATOR;
        $items = glob($dir . "*", GLOB_ONLYDIR);

        foreach ($items as $k => $f) {
            $label     = str_replace($dir, "", $f);
            $classPath = $f . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . ucfirst($label) . 'Command.php';

            if (is_file($classPath)) {
                $cmdsDir = $f . DIRECTORY_SEPARATOR . "commands" . DIRECTORY_SEPARATOR;
                $cmds    = self::listCmdInDir('app.' . $label, $cmdsDir);

                foreach ($cmds as $cmd) {
                    $parts                                     = explode(".", $cmd['url']);
                    $shortUrl                                  = end($parts);
                    $list['App - ' . $parts[2]][$cmd['class']] = $shortUrl;
                }
            }
        }

        if ($devMode) {
            /*
			** Fetching all command files inside plansys.commands
			**/
            $cmdsDir = Yii::getPathOfAlias("application.commands") . DIRECTORY_SEPARATOR;
            $cmds    = self::listCmdInDir('plansys', $cmdsDir);
            foreach ($cmds as $cmd) {
                $shortUrl                       = Helper::explodeLast(".", $cmd['url']);
                $list['Plansys'][$cmd['class']] = $shortUrl;
            }


            /*
            ** Fetching all command files inside plansys.modules
            **/

            $dir   = Yii::getPathOfAlias("application.modules") . DIRECTORY_SEPARATOR;
            $items = glob($dir . "*", GLOB_ONLYDIR);

            foreach ($items as $k => $f) {
                $label     = str_replace($dir, "", $f);
                $classPath = $f . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . ucfirst($label) . 'Command.php';

                if (is_file($classPath)) {
                    $cmdsDir = $f . DIRECTORY_SEPARATOR . "commands" . DIRECTORY_SEPARATOR;
                    $cmds    = self::listCmdInDir('plansys.' . $label, $cmdsDir);

                    foreach ($cmds as $cmd) {
                        $parts                                         = explode(".", $cmd['url']);
                        $shortUrl                                      = end($parts);
                        $list['Plansys - ' . $parts[2]][$cmd['class']] = $shortUrl;
                    }
                }
            }
        }

        return $list;

    }


    private static function listCmdInDir($module, $cmdDir) {
        $cmdRaw = glob($cmdDir . "*.php");
        $cmds   = [];
        $path   = explode(".", $module);
        $m      = $path[0] != "plansys" ? "app" : "application";

        $commandPath = "";
        if (count($path) == 1) {
            $commandPath = $m . ".commands.";
        } else if (count($path) == 2) {
            $commandPath = $m . ".modules." . $path[1] . ".commands.";
        }

        foreach ($cmdRaw as $cmd) {
            $c      = str_replace([$cmdDir, ".php"], "", $cmd);
            $cmds[] = [
                'label' => $c,
                'icon' => 'fa-slack',
                'active' => @$_GET['active'] == $module . '.' . $c,
                'url' => Yii::app()->controller->createUrl('/dev/processManager/create', [
                    'active' => $module . '.' . $c
                ]),
                'class' => $commandPath . $c,
                'target' => 'col2'
            ];
        }
        return $cmds;
    }

    public static function createSettingsId($processName) {
        return str_replace(" ", "_", strtolower($processName)) . "_" . md5(date('DD-mm-yyyy h:i:S'));
    }

    public static function periodConverter($period, $periodType) {
        switch ($periodType) {
            case 'secondly':
                $period *= 1;
                break;
            case 'minutely':
                $period *= 60;
                break;
            case 'hourly':
                $period *= 3600;
                break;
            case 'daily':
                $period *= 86400;
                break;
            default:
                return null;
                break;
        }
        return $period;
    }

    public static function findRunningProcess($pid) {
        $output = null;
        $return = null;

        chdir(Yii::getPathOfAlias('application'));
        exec('process find ' . $pid, $output, $return);

        return ((strtolower(trim($output[0])) == 'false') ? false : true);
    }


    public static function getProcessCommand() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'process.exe';
        } else if (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN'){
            return './process.osx';
        }
    }

    public static function run($name, $command, $runOnce = TRUE) {
        if(!ProcessHelper::isPMRunning()){
            return false;
        }

        $pid     = [];
        $process = ProcessHelper::getProcessCommand();


        $id = ProcessHelper::createSettingsId($name);        

        chdir(Yii::getPathOfAlias('application'));
        exec($process . ' run ' . $command, $pid);
        echo $process . ' run ' . $command;

        if (!empty($pid)){ 
            # Default value of Processes
            Setting::set("process.".$id.".name", $name);
            Setting::set("process.".$id.".command", $command);        
            Setting::set("process.".$id.".period", null);
            Setting::set("process.".$id.".periodType", null);            
            Setting::set("process.".$id.".periodCount", null);            

            Setting::set("process.".$id.".lastRun", time());            
            Setting::set("process.".$id.".isStarted", true);            
            Setting::set("process.".$id.".pid", $pid[0]);  
            Setting::set("process.".$id.".file", null);             
            Setting::set("process.".$id.".runOnce", $runOnce);

            return $pid[0];
        }else{ 
            return false;
        }
    }

    public static function kill($pid) {
        $output = null;
        $return = null;

        chdir(Yii::getPathOfAlias('application'));
        $process = ProcessHelper::getProcessCommand();
        exec($process . ' kill ' . $pid, $output, $return);

        return $output;
    }

    public static function isPMRunning(){
        $return = Setting::get('processManager.isRunning', false, true);        
        return $return;
    }
}

?>