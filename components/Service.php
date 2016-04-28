<?php

class Service extends CConsoleCommand {
    public $service = null;
    public $pid = 0;
    public $id = "";
    public $params = null;
    
    public function init() {
        if (isset($GLOBALS['svc'])) {
            $this->service = $GLOBALS['svc'];
            $this->pid = $this->service['pid'];
            $this->id = $GLOBALS['svc_id'];
            $this->service['id'] = $this->id;
            
            unset($GLOBALS['svc']);
            unset($GLOBALS['svc_id']);
            ServiceManager::initLogAppend($this->id, "[OK]");
            ServiceManager::markAsRunning($this->service['name'], $this->id, $this->service);
            
            if (isset($this->service['params'])) {
                $this->params = $this->service['params'];
            }
            
            ob_start(function($data) {
                $this->logAppend("\n" . $data);
                return "";
            }, 1);
        } else {
            echo <<<EOF
============================ WARNING ==========================
  You should start this service from Plansys Service Manager
===============================================================

EOF;
        }
    }
    
    protected function afterAction($action, $params, $exitCode=0) {
        if (isset($this->service['name'])) {
            $this->log("SERVICE: Service {$this->service['name']} exited with code {$exitCode} [PID: {$this->pid}] ");
            ServiceManager::markAsStopped($this->service['name'], $this->id);
        }
        return parent::afterAction($action, $params, $exitCode);
    }
    
    public function setView($key, $value) {
        if (!isset($this->service['view'])) {
            $this->service['view'] = [];
        }
        $this->service['view'][$key] = $value;
        $path = Yii::getPathOfAlias('root.assets.services.init') . DIRECTORY_SEPARATOR . $this->id;
        
        if (is_file($path)) {
            file_put_contents($path, json_encode($this->service, JSON_PRETTY_PRINT));
        }
    }
    
    public function log($msg) {
        ServiceManager::log($this->service['name'], $this->id, $msg);
    }
    
    public function logAppend($msg) {
        ServiceManager::logAppend($this->service['name'], $this->id, $msg);
    }
}