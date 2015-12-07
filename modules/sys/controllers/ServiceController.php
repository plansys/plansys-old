<?php

class ServiceController extends Controller {
    public $defaultAction = 'start';
    
    public function actionView($name, $id = null, $full = 0) {
        $options = [];
        if ($full != 0) {
            $options['layout'] = '//layouts/blank';
        }
        
        $svc = $this->getSvc($name, $id);
        $this->renderForm("SysServiceView",null, [
            'svc' => $svc,
            'name' => $name, 
            'id' => $id
        ], $options);
    }
    
    public function actionPool($name, $id = null) {
        $svc = $this->getSvc($name, $id);
        if (is_null($svc)) {
            $log = ServiceManager::getStoppedInstance($name, $id);
            if (!!$log) {
                if (isset($log['view']) && 
                    (isset($log['view']['failed']) || isset($log['view']['finished']))) {
                    $svc = $log;
                }
            }
        }
        
        echo json_encode([
            'svc' => $svc,
            'name' => $name, 
            'id' => $id
        ]);
    }
    
    public function actionInstanceNotFound($full = 0) {
        $options = [];
        if ($full != 0) {
            $options['layout'] = '//layouts/blank';
        }
        
        $this->renderForm("SysServiceNotFound", null, [
            'msg' => 'Service instance does not exist'
        ], $options);
    }
    
    public function actionStart($name) {
        ServiceManager::start($name, $_GET);
    }
    
    public function getSvc($name, $id = null) {
        $svc = [];
        if (!is_null($id)) {
            $svc = ServiceManager::getInstance($id);
        } else if (!is_null($name)) {
            $svc = ServiceManager::getInstanceByName($name);
        }
        return $svc;
    }
}