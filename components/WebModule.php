<?php

class WebModule extends CWebModule {

    public function accessControl($controller, $action) {
        
    }

    public function beforeControllerAction($controller, $action) {
        parent::beforeControllerAction($controller, $action);
        $this->accessControl($controller, $action);
        return true;
    }

}
