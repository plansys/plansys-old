<?php

class DevModule extends CWebModule {

    public $newFileMode = 0666;
    public $newDirMode  = 0777;

    public function accessControl($controller,$action) {
        
    }

    public function init() {
        // import the module-level controllers and forms
        $import = array(
            'application.modules.dev.controllers.*',
            'application.modules.dev.forms.*',
            'application.modules.dev.forms.formbuilder.*',
            'application.modules.dev.forms.formbuilder.crud.*',
            'application.modules.dev.forms.genctrl.*',
            'application.modules.dev.forms.genmenu.*',
            'application.modules.dev.forms.genmodel.*',
            'application.modules.dev.forms.genmodule.*',
            'application.modules.dev.forms.service.*',
            'application.modules.dev.forms.settings.*',
            'application.modules.dev.forms.settings.js.*',
            'application.modules.dev.forms.users.*',
            'application.modules.dev.forms.users.role.*',
            'application.modules.dev.forms.users.user.*',
            'application.modules.dev.forms.users.user.js.*'
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