<?php

class ServiceController extends Controller {
    
    public function actionIndex() { 
        ## cek whether php path is present
        $php = Setting::get('app.phpPath');
        if ($php != '' && !is_file($php)) {
            echo "INVALID PHP PATH: " . $php;
            die();
        }
        if ($php == '') {
            $php = 'php';
        }
        
        ## check if php version is the same
        $out = '';
        exec($php . ' -v', $output);
        if (!empty($output)) {
            $match = false;
            $version = "INVALID OUTPUT";
            foreach ($output as $o) {
                if (strpos($o, phpversion()) !== false) {
                    $match = true;
                    $version = $o;
                    break;
                }
            }
            
            if (!$match) {
                echo "DIFFERENT PHP VERSION BETWEEN WEB SERVER (" . phpversion() . ") AND CLI (" . $version . ")";
                die();
            } 
        } else {
            echo "FAILED TO EXECUTE: {$php} -v <br/><span style='color:red;'>Please check your php path in settings</span>";
            die();
        }
           
        $model = new DevServiceIndex();
        $model->status = "Service Daemon Running" ;
        $this->renderForm('DevServiceIndex', $model);
    }
    
    public function actionDelete($m) {
        ServiceManager::remove($m);
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
        $class = $m . "." . $c . "Command";
        Yii::import($class);
        
        $refl = new ReflectionClass($c . "Command");
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

    public function actionStart($n){
        try {
            $res =  ServiceManager::start($n);
        } catch(CException $e) {
            echo ($e->getMessage());
        }
    }

    public function actionStop($n){
        ServiceManager::stop($n);
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
                $model->status = "ok";
                $model->save();
                echo <<<EOF
<script>
    window.opener.formScope.model.action = '{$model->action}';
    window.opener.formScope.model.schedule = '{$model->schedule}';
    window.opener.formScope.model.period = '{$model->period}';
    window.opener.formScope.model.instance = '{$model->instanceMode}';
    window.opener.formScope.model.singleIntanceMode = '{$model->singleInstanceAction}';
    
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
        $this->renderForm('DevServiceForm',$model);
    }
    
    public function actionDetail($id = null) {
        $a = json_encode(ServiceManager::getService($id));
        $a = json_decode($a, true);
        $a['runningInstances'] = array_values($a['runningInstances']);
        $a['stoppedInstances'] = array_values($a['stoppedInstances']);
        echo json_encode($a);
    }

    public function actionSave(){
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        
        $model= DevService::load($post['id']);
        if ($model !== false) {
            $filePath= ServiceManager::getFilePath($model);
            if (is_file($filePath)) {
                file_put_contents($filePath, $post['content']);
            }
        }
    }

}