<?php

class PMCommand extends CConsoleCommand {

    public function actionIndex($force = false){            
        $isPMRunning = null;

        while(true){   

            if(!$force) $isPMRunning = ProcessHelper::isPMRunning();
            
            if(!$isPMRunning) break;

            $cmds           = Setting::get('process', null, true);            

            foreach ($cmds as $id => $cmd) {                        
                $curTime = time();
                $isStillRunning = false;                

                # Getting PID of previous process.
                if(isset($cmd['pid'])) $isStillRunning = ProcessHelper::findRunningProcess($cmd['pid']);                

                # Remove one task kill process from Process Manager List
                if($cmd['runOnce'] && !$isStillRunning) Setting::remove('process.'.$id);                               

                if($cmd['runOnce']){  
                    echo $cmd['name']."\n";
                    continue;   
                } 

                                
                $isStarted = $cmd['isStarted'];

                # Running periodic process continously
                if((!isset($cmd['lastRun']) || abs($curTime-$cmd['lastRun'])%$cmd['periodCount']==0) && !$isStillRunning && $isStarted && !$cmd['runOnce']){
                    chdir(Yii::getPathOfAlias('application'));

                    exec("process run yiic ".$cmd['command'], $pid);                    
                    
                    $cmd['lastRun'] = $curTime; 
                    $cmd['pid']     = $pid[0];

                    Setting::set('process.'.$id, $cmd);                   
                    // Logging
                    // echo "[".date('d-m-Y H:i:s')."] ".$cmd['name']." is running\n";                    
                }                                
            }          

            sleep(1);
        }
    }       

}
