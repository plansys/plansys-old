<?php


class ServiceApiController extends Controller {
    public function actionStart($name) {
        ServiceManager::start($name, $_GET, false);
    }
    
    public function actionStop($name) {
        ServiceManager::kill($name);
    }
    
    public function actionInfo($name) {
        $svc = ServiceManager::getService($name);
        echo json_encode([
            'lastrun' => date("Y-m-d H:i:s", $svc->lastRun),
            'instances'=> count($svc->runningInstances)
        ]);
    }
    
    public function actionTime() {
        echo date("Y-m-d H:i:s");
    }
}