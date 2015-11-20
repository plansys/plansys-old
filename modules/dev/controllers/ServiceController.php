<?php

class ServiceController extends Controller {
    
    public function actionIndex() {                         
        $isRunning = Setting::get('services.daemon.isRunning', false);
        
        $model = new DevServiceIndex();
        $model->status = $isRunning ? "Service Daemon Running" : "Service Daemon Stopped";
        $this->renderForm('DevServiceIndex', $model);
    }
    
    public function actionListCommand($m) {
        $path = Yii::getPathOfAlias($m) . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        $dirs = glob($path . "*Command.php");
        
        if (!empty($dirs)) {
            $results = [
                '' => '-- Choose Command --',
                '---' => '---'
            ];
            
            foreach($dirs as $r) {
                $class = str_replace([$path, "Command.php"], "", $r) . "Command";
                $results[$class] = $class;
            }
            echo json_encode($results);
        } 
    }
    
    public function actionListAction($m, $c, $n = "t"){
        $class = $m . "." . $c;
        Yii::import($class);

        $refl = new ReflectionClass($c);
        $methods = $refl->getMethods();
        
        if (!empty($methods)) {
            if ($n== "y") {
                $results = [
                    '' => '-- Choose Command --',
                    '---' => '---'
                ];
            } else {
                $results = [];
            }
            
            foreach ($methods as $m) {
                if (substr($m->name,0,6) == "action") {
                    $results[$m->name] = $m->name;
                }
            }
            
            
            if (count($results) > 2 || $n == "t") {
                echo json_encode($results);
            }
        }
    }

    public function actionStartDaemon() {    
        $isRunning = Setting::get('services.daemon.isRunning', false);
        if(!$isRunning){
            ServiceManager::startDaemon();
        }     
    }

    public function actionStopDaemon() {    
        $isRunning = Setting::get('services.daemon.isRunning', false);
        if(!!$isRunning){
            ServiceManager::stopDaemon();
        }     
    }

    public function actionStart($n){
        ServiceManager::runInternal($n);
    }

    public function actionStop($n){
        ServiceManager::kill($n);
    }
    
    public function actionMonitor($n) {
        echo json_encode(ServiceManager::getRunningInstance($n));
    }
    
    public function actionReadLog($n, $id, $l = 20) {
        if ($id == 'false') {
            echo ServiceManager::readLog($n, null, $l);
        }
        echo ServiceManager::readLog($n, $id, $l);
    }
    
    public function actionCreate(){    
        $model = new DevService;
        $href = '';
        if(isset($_POST['DevService'])){
            $model->attributes = $_POST['DevService'];
            if ($model->validate()) {
                $model->save();
                $href = Yii::app()->createUrl("dev/service/update",[
                    "id"=> $model->name
                ]);
            }
        }

        $this->renderForm('DevService', $model , [
            'href' => $href,
            'isNewRecord' => true
        ],[
            'layout'=>'//layouts/blank'
        ]);
    }
    
    public function actionEditService($id = null) {
        $model = DevService::load($id);
        $href = '';
        if(isset($_POST['DevService'])){
            $model->attributes = $_POST['DevService'];
            if ($model->validate()) {
                $model->save();
                
                echo <<<EOF
<script>
    window.opener.formScope.model.action = '{$model->action}';
    window.opener.formScope.model.schedule = '{$model->schedule}';
    window.opener.formScope.model.period = '{$model->period}';
    window.opener.formScope.model.instance = '{$model->instance}';
    window.opener.formScope.model.singleIntanceMode = '{$model->singleInstanceMode}';
    
    window.opener.formScope.status = '[Service succesfully updated]'; 
    window.close();
</script>
EOF;
                
                die();
            }
        }

        $this->renderForm('DevService', $model , [
            'href' => $href,
            'isNewRecord' => false
        ],[
            'layout'=>'//layouts/blank'
        ]);
    }
    
    public function actionUpdate($id = null){      
        $model = DevServiceForm::load($id);
        if (!$model) {
            throw new CHttpException(404);
        }
        Asset::registerJS('application.static.js.lib.ace');
        $instances = ServiceManager::getRunningInstance($id);
        $this->renderForm('DevServiceForm',$model, [
            'isRunning' => count($instances) > 0,
            'instances' => $instances,
            'log' => ServiceManager::readLog($id)
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
        
        $model= DevService::load($post['id']);
        if ($model !== false) {
            $filePath= $model->filePath;
            if (is_file($filePath)) {
                file_put_contents($filePath, $post['content']);
            }
        }
    }

}