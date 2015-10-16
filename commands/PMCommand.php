<?php

class PMCommand extends CConsoleCommand {

    public function actionIndex(){            
        while(true){                    
            $cmds    = Setting::get('process', null, true);            

            foreach ($cmds as $id => $cmd) {                        
                $curTime = time();
                $isStillRunning = false;                

                if(isset($cmd['pid'])){
                    $isStillRunning = ProcessHelper::findRunningProcess($cmd['pid']);
                }
                                
                $isStarted = $cmd['isStarted'];

                if((!isset($cmd['lastRun']) || abs($curTime-$cmd['lastRun'])%$cmd['periodCount']==0) && !$isStillRunning && $isStarted){                    
                    chdir(Yii::getPathOfAlias('application'));
                    exec("process run yiic ".$cmd['command'], $pid);

                    $cmd['lastRun'] = $curTime; 
                    $cmd['pid']     = $pid[0];

                    Setting::set('process.'.$id, $cmd);                   
                    // Logging
                    //echo "[".date('d-m-Y H:i:s')."] ".$cmd['name']." is running\n";                    
                }                
            }          

            sleep(1);
        }
    }       

}
