<?php

class SysModule extends CWebModule {

    public function init() {
        // import the module-level controllers and forms
        $this->setImport(array(
            'application.modules.sys.controllers.*',
            'application.modules.sys.forms.*'
        ));
    }
    
    
    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            if (Yii::app()->user->isGuest) {
                throw new CHttpException(403, "Anda tidak memiliki hak untuk mengakses halaman ini.");
            }

            return true;
        } else
            return false;
    }

}