<?php

class AdminModule extends CWebModule {

    public function init() {
        // import the module-level models and components
        $this->setImport(array(
            'application.models.*',
            'admin.models.*',
            'admin.forms.*',
            'admin.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            if (Yii::app()->user->role != "ADMIN") {
                throw new CHttpException(403, "Anda tidak memiliki hak untuk mengakses halaman ini.");
            }

            return true;
        } else
            return false;
    }

}
