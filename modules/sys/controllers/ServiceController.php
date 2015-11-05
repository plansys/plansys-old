<?php

class ServiceController extends Controller {
    public $defaultAction = 'start';
    
    public function actionView($name = null, $id = null, $l = 1) {
        $params = [];
        $options = [];
        if ($l == 0) {
            $options['layout'] = '//layouts/blank';
        }
        
        if (!is_null($id)) {
            $params['svc'] = ServiceManager::getInstance($_GET['id']);
        } else if (!is_null($name)) {
            $params['svc'] = ServiceManager::getInstanceByName($_GET['name']);
        }
        
        if (!isset($params['svc'])) {
            throw new CHttpException(404);
        }
        
        
        $params['title'] = $params['svc']['name'];
        $params['id'] = $params['svc']['id'];
        $this->renderForm("SysServiceForm",null, $params, $options);
    }
    
    public function actionStart($name, $l = 1) {
        $params = ['svc'=>null];
        $options = [];
        if ($l == 0) {
            $options['layout'] = '//layouts/blank';
        }
        
        $params['id'] = ServiceManager::run($name, $_GET);
        
        $this->renderForm("SysServiceForm",null, $params, $options);
    }
    
    public function actionPool($name, $id) {
        echo json_encode([
            'title' => $title,
            'percent' => $percent,
            'msg' => $msg
        ]);
    }
}