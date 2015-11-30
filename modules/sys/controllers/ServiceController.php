<?php

class ServiceController extends Controller {
    public $defaultAction = 'start';
    
    public function actionTest() {
        $transaction = Yii::app()->db->beginTransaction();
        $errors = [];
        try {
            for ($i = 0; $i < 4;$i++) {
                $test1 = new Test;
                $test1->attributes = [
                    'name' => '10',
                    'user_id' => '123',
                    'no' =>'123',
                    'date' => '2015-01-01',
                    'datetime' => '2015-01-01 10:10:10',
                    'varchar' => 'oke'
                ];
                $test1->save();
                if ($test1->hasErrors()) {
                    $errors[] = $test1->errors;
                }
            }
            if (empty($errors)) {
                $transaction->commit();
            } else {
                var_dump($errors);die();
                $transaction->rollback();
            }
            
        } catch(Exception $e) {
            
            $transaction->rollback();
        }
        
    }
    
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
                if (isset($log['view']) && isset($log['view']['failed'])) {
                    $svc = $log;
                } else {
                    $svc = "finished";
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