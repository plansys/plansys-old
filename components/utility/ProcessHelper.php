<?php 
class ProcessHelper extends CComponent {
	
	public static function listAllCmdForGridView(){		
		$prcs = Setting::get("process");
		$data = [];
		foreach ($prcs as $id=>$prc) {
			$data[] = [
				'id'=>$id,
				'file'=>$prc['file'],
				'name'=>$prc['name'], 
				'command'=>$prc['command'], 
				'period'=>$prc['period'], 
				'periodType'=>$prc['periodType'],
                'lastRun' => $prc['lastRun'],
                'isStarted' => $prc['isStarted']
			];
		}
		
		return $data;
	}

	public static function listCmdForMenuTree() {
        $list    = [];
        $devMode = Setting::get('app.mode') === "plansys";
              
        if ($devMode) {
        	/*
			** Fetching all command files inside plansys.modules			
			**/ 

            $dir         = Yii::getPathOfAlias("application.modules") . DIRECTORY_SEPARATOR;
            $items       = glob($dir . "*", GLOB_ONLYDIR);
            $plansysList = [];

            foreach ($items as $k => $f) {
                $label     = str_replace($dir, "", $f);
                $classPath = $f . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR  . ucfirst($label) . 'Command.php';
                
                if (is_file($classPath)) {
                    $cmdsDir = $f . DIRECTORY_SEPARATOR . "commands" . DIRECTORY_SEPARATOR;
                    $cmds    = self::listCmdInDir('plansys.' . $label, $cmdsDir);                    

                    foreach ($cmds as $cmd) {
                    	$plansysList['Module Commands'][$cmd['url']] = $cmd['class'];
                    }
                                        
                }
            }       

            /*
			** Fetching all command files inside plansys.commands
			**/            
            $cmdsDir = Yii::getPathOfAlias("application.commands") . DIRECTORY_SEPARATOR;
            $cmds    = self::listCmdInDir('plansys', $cmdsDir);                        
            foreach ($cmds as $cmd) {
                $plansysList['Commands'][$cmd['url']] = $cmd['class'];
            }                        

            $list=$plansysList;
            

        }
                       
        return $list;

    }


	private static function listCmdInDir($module, $cmdDir) {
        $cmdRaw = glob($cmdDir . "*.php");
        $cmds   = [];
        $path    = explode(".", $module);
        $m       = $path[0] != "plansys" ? "app" : "application";

        $commandPath = "";
        if (count($path) == 1) {
            $commandPath = $m . ".commands.";
        } else if (count($path) == 2) {
            $commandPath = $m . ".modules." . $path[1] . ".commands.";
        }

        foreach ($cmdRaw as $cmd) {
            $c       = str_replace([$cmdDir, ".php"], "", $cmd);
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

    public static function createSettingsId($processName){
    	return str_replace(" ", "_", strtolower($processName))."_".md5(date('DD-mm-yyyy h:i:S'));
    }

    public static function periodConverter($period, $periodType){
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

    public static function findRunningProcess($pid){        
        $output = null;
        $return = null;

        chdir(Yii::getPathOfAlias('application'));
        exec('process find '.$pid, $output, $return);        

        return ((strtolower(trim($output[0]))=='false')?false:true);
    }

    public static function kill($pid){            
        $output = null;
        $return = null;

        chdir(Yii::getPathOfAlias('application'));
        exec('process kill '.$pid, $output, $return);        

        return $output;        
    }
}
?>