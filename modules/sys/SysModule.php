<?php

class SysModule extends CWebModule {

    public function init() {
        // import the module-level models and components
        $this->setImport(array(
            'application.models.*',
            'application.modules.sys.controllers.*',
            'application.modules.sys.forms.*',
            'application.modules.sys.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
//            if (Yii::app()->user->role != "sys") {
//                throw new CHttpException(403, "Anda tidak memiliki hak untuk mengakses halaman ini.");
//            }

            return true;
        } else
            return false;
    }

}
