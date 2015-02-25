<?php

class DevModule extends CWebModule {

    public function init() {
        // import the module-level models and components
        $this->setImport(array(
            'application.models.*',
            'application.modules.dev.controllers.*',
            'application.modules.dev.forms.*',
            'application.modules.dev.forms.formbuilder.*',
            'application.modules.dev.forms.generators.*',
            'application.modules.dev.forms.generators.templates.*',
            'application.modules.dev.forms.users.user.*',
            'application.modules.dev.forms.users.role.*',
            'application.modules.dev.components.*',
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
