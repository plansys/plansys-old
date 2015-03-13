<?php

class DevModule extends CWebModule {

    public function init() {
        $import = array(
            'application.models.*',
            'application.modules.dev.controllers.*',
            'application.modules.dev.forms.*',
            'application.modules.dev.forms.formbuilder.*',
            'application.modules.dev.forms.genmodule.*',
            'application.modules.dev.forms.generators.*',
            'application.modules.dev.forms.generators.templates.*',
            'application.modules.dev.forms.users.user.*',
            'application.modules.dev.forms.users.role.*',
            'application.modules.dev.forms.settings.*',
            'application.modules.dev.components.*',
        );

        if (is_dir(Yii::getPathOfAlias('app.modules.dev.forms.users.user.*'))) {
            $import[] = 'app.modules.dev.forms.users.user.*';
        }

        // import the module-level models and components
        $this->setImport($import);
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            if (Yii::app()->user->isGuest) {
                throw new CHttpException(403);
            }

            return true;
        } else
            return false;
    }

}