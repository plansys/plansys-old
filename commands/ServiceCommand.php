<?php

class ServiceCommand extends CConsoleCommand {
    public $defaultAction = 'startDaemon';
    
    public function actionStartDaemon(){
        ServiceManager::markDaemonAsRun();
        $i = 1;
        while(true){   
            $services = Setting::get('services.list', [], true);
            $curTime = time();
            foreach ($services as $serviceName => $service) {
                $lastRun = strtotime(@$service['lastRun']);
                
                if ($service['schedule'] != 'manual') {
                    switch ($service['schedule']) {
                        case "day":
                            $period = $service['period'] * 86400;
                            break;
                        case "hour":
                            $period = $service['period'] * 3600;
                            break;
                        case "minute":
                            $period = $service['period'] * 60;
                            break;
                        case "second":
                            $period = $service['period'] ;
                            break;
                    }
                    if((!isset($service['lastRun']) || abs($curTime-$lastRun)% $period==0)){
                        ServiceManager::runInternal($serviceName, $service);
                    }
                } 
            }
            if ($i == 5) {
                $i = 0;
                ServiceManager::markDaemonAsRun();
            }

            sleep(1);
            $i++;
        }
    }
    
    public function actionTest() {
        ServiceManager::run("Test");
    }
    
    public function actionExecute($id){
        ServiceManager::initLogAppend($id, "[OK]");
        
        $running = true;
        while ($running) {
            if (ServiceManager::hasMsg($id)) {
                $svc = ServiceManager::readMsg($id);
                if (is_array($svc)) {
                    $running = false;
                    $commandPath = Yii::getPathOfAlias($svc['commandPath']);
                    $commandName = lcfirst(substr($svc['command'],0,strlen($svc['command'])-7));
                    $actionName = lcfirst(substr($svc['action'],6));
                    $GLOBALS['svc'] = $svc;
                    $GLOBALS['svc_id'] = $id;
                    
                    ServiceManager::initLog($id, str_pad("Starting {$svc['name']} [PID:{$svc['pid']}]", 45, "."));
                    
                    $runner = new CConsoleCommandRunner();
                    $runner->addCommands($commandPath);
                    $runner->run(['yiic', $commandName, $actionName]);
                }
            }
        }
    }
}
