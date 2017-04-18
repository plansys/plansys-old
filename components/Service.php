<?php

class Service extends CConsoleCommand {
    public $params = null;
    public $_sname = null;
    
    public function beforeAction($action, $params) {
        Setting::initPath();
        
        if (is_string($this->params)) {
            $jsonparams = json_decode($this->params, true);
            if (!is_null($jsonparams)) {
                $this->params = $jsonparams;
            }
        }
        return true;
    }
    
    public function setView($key, $value) {
        ServiceManager::setView($this->_sname, $key, $value);
    }
}