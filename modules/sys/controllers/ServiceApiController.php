<?php


class ServiceApiController extends Controller {
    public function actionStart($name) {
        ServiceManager::start($name, $_GET, false);
    }
    
    public function actionStop($name) {
        ServiceManager::kill($name);
    }
    
    public function actionInfo($name) {
        
        $svc = ServiceSetting::get('list.' . $name);
        echo json_encode([
            'lastrun' => $svc['lastRun'],
            'instances'=> count(ServiceManager::getRunningInstance($name))
        ]);
    }
    
    public function actionTime() {
        echo date("Y-m-d H:i:s");
    }
}