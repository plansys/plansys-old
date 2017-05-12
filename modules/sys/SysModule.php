<?php

class SysModule extends CWebModule {
    public function accessControl($controller, $action) {
        
    }

    public function init() {
        // import the module-level controllers and forms
        $this->setImport([
            'application.modules.sys.controllers.*',
            'application.modules.sys.forms.*'
        ]);
    }
    
    
    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            if (Yii::app()->user->isGuest) {
                if ($controller->id != 'serviceApi') {
                    echo 'NO GUEST ACCESS';
                    die();
                }
            }

            return true;
        } else {             return false;
        }
    }

}
