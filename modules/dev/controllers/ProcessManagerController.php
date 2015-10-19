<?php

class ProcessManagerController extends Controller {
    
    public function actionIndex() {        
        $this->actionList();
    }

    public function actionList() {                    
        $pmIsRunning = Setting::get('processManager.isRunning', false);
        $this->renderForm('settings.DevSettingsProcessManager', null, [
            'pmIsRunning'=>$pmIsRunning
        ]);
    }

    public function actionStart() {    
        $isRunning = Setting::get('processManager.isRunning', false);
        if(!$isRunning){
            $pid = null;
            chdir(Yii::getPathOfAlias('application'));

            //Starting processManager
            Setting::set('processManager.isRunning', true);
            exec('process run yiic pm', $pid);

            if(isset($pid)){
                Setting::set('processManager.pid', $pid[0]);
            }
        }     
        $this->redirect(['/dev/processManager/']);
    }

    public function actionStop() {    
        $isRunning = Setting::get('processManager.isRunning', false);
        if($isRunning){
            $pid = Setting::get('processManager.pid');
            chdir(Yii::getPathOfAlias('application'));            

            //Stopping processManager
            Setting::set('processManager.isRunning', false);
            ProcessHelper::kill($pid);
            Setting::set('processManager.pid', null);

            //Stopping running child process
            $prcs = Setting::get('process');
            foreach ($prcs as $id=>$prc) {
                $this->actionStopProcess($id);
            }
        }     
        $this->redirect(['/dev/processManager/']);
    }

    public function actionStartProcess($id){
        $prc = Setting::get('process.'.$id);        
        if(!$prc['isStarted']){            
            Setting::set('process.'.$id.'.isStarted', true);
        }
        $this->redirect(['/dev/processManager/']);
    }

    public function actionStopProcess($id){
        $prc = Setting::get('process.'.$id);
        if($prc['isStarted']){
            Setting::set('process.'.$id.'.isStarted', false);
            if(isset($prc['pid'])){
                ProcessHelper::kill($prc['pid']);
                Setting::set('process.'.$id.'.pid', null);
            }            
        }
        $this->redirect(['/dev/processManager/']);
    }
    
    public function actionCreate(){        
        $href = '';
        $name = '';
        $command = '';

        if(isset($_POST['DevSettingsProcessManagerPopUp'])){
            $cmd    = $_POST['DevSettingsProcessManagerPopUp'];            
            $file   = end(explode("=", $_POST['processFile']));            
            $prefix = Helper::explodeFirst("-", Helper::camelToSnake(Helper::explodeLast(".", $file)));

            //Creating unique id for setting 
            $id     = ProcessHelper::createSettingsId($cmd['processName']);            

            Setting::set("process.".$id.".name", $cmd['processName']);
            Setting::set("process.".$id.".command", $prefix . " " . $cmd['processCommand']);
            Setting::set("process.".$id.".period", $cmd['processPeriod']);
            Setting::set("process.".$id.".periodType", $cmd['processPeriodType']);            
            Setting::set("process.".$id.".periodCount", ProcessHelper::periodConverter($cmd['processPeriod'], $cmd['processPeriodType']));            

            // Default process value
            Setting::set("process.".$id.".lastRun", null);            
            Setting::set("process.".$id.".isStarted", false);            
            Setting::set("process.".$id.".pid", null);            
            Setting::set("process.".$id.".file", $file);            
            Setting::set("process.".$id.".runOnce", false);

            $href   = Yii::app()->createUrl("dev/processManager/update",["id"=> $id, "active" => $file]);            

        }

        $this->renderForm('settings.DevSettingsProcessManagerPopUp',null, ['href' => $href],[
            'layout'=>'//layouts/blank'
        ]);
    }
    
    public function actionUpdate($id = null){        
        $content = '';
        $name    = '';
        $command = '';
        $commandFull  = '';
        $commandPrefix= '';
        $file    = '';
        $period  = '';
        $periodType  = '';
        $cmd = [];

        if (isset($id)) {   
            $cmd = Setting::get('process.'.$id);            
            
            if(count($cmd) > 0){                                
                $id         = $id;
                $name       = $cmd['name'];
                $commandFull= $cmd['command'];
                $tmp        = explode(" ", $cmd['command']);
                $commandPrefix= array_shift($tmp);
                $command    = implode(" ", $tmp);
                $period     = $cmd['period'];
                $periodType = $cmd['periodType'];
                $file       = $cmd['file'];
                $prefix     = Helper::explodeFirst("-", Helper::camelToSnake(Helper::explodeLast(".", $file)));
                
                //$filePath = Yii::getPathOfAlias((count($path)>2 ? "application.modules.". $path[1] . ".commands." . $path[2]  : "application.commands.". $path[1])) . ".php";                                
                $filePath = Yii::getPathOfAlias($file) . ".php";                                                
                $content = file_get_contents($filePath);                
            }else{
                $this->redirect(['/dev/processManager/']);
            }
        }else{
            $this->redirect(['/dev/processManager/']);
        }

        if(isset($_POST['DevSettingsProcessManagerForm'])){

            $cmd     = $_POST['DevSettingsProcessManagerForm'];
            $id      = $_POST['processSettingsId'];            
            $prefix  = $_POST['processCommandPrefix'];            
            
            Setting::set("process.".$id.".name", $cmd['processName']);
            Setting::set("process.".$id.".command", $prefix.' '.$cmd['processCommand']);
            Setting::set("process.".$id.".period", $cmd['processPeriod']);
            Setting::set("process.".$id.".periodType", $cmd['processPeriodType']);                        
            Setting::set("process.".$id.".periodCount", ProcessHelper::periodConverter($cmd['processPeriod'], $cmd['processPeriodType']));

            $this->redirect(['/dev/processManager/']);
        }

        Asset::registerJS('application.static.js.lib.ace');
        $this->renderForm('settings.DevSettingsProcessManagerForm', [
            'content' => $content,            
            'name' => $file,
            'prefix' => $name,
            'processName' => $name,
            'processCommand' => $command,
            'processCommandPrefix' => $commandPrefix,
            'processCommandFull' => $commandFull,
            'processSettingsId' => $id,
            'periodType'=>$periodType,
            'period' =>$period
        ]);
    }

    public function actionDelete($id = null){
        if(isset($id)){            
            Setting::remove('process.'.$id);            
        }

        $this->redirect(['/dev/processManager/']);
    }

    public function actionSave(){
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $path     = explode(".", $post['active']);
        $filePath = Yii::getPathOfAlias((count($path)>2 ? "application.modules.". $path[1] . ".commands." . $path[2]  : "application.commands.". $path[1])) . ".php";                                

        if (is_file($filePath)) {
            file_put_contents($filePath, $post['content']);
        }
    }

    public function actionSandbox(){
        echo "Process Sandbox<br/>";
        $pid = ProcessHelper::run("Testing Run Once", "yiic test test --name=abcd");
        
        if(!$pid){
            echo "Unable to run command, please check Process Manager";
        }
    }

}