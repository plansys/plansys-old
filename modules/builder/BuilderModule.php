<?php

class BuilderModule extends CWebModule {

    public $newFileMode = 0666;
    public $newDirMode  = 0777;

    public $defaultController = "Builder";
    
    public function accessControl($controller,$action) {
        ####### PLANSYS GENERATED CODE: START #######
        #######    DO NOT EDIT CODE BELOW     #######
        $accessType = "DEFAULT";
        $defaultRule = "deny";
        $rolesRule = [
            "deny" => [],
            "allow" => [
                "1"
            ],
            "custom" => []
        ];
        $usersRule = [
            "deny" => [],
            "allow" => [],
            "custom" => []
        ];
        #######    DO NOT EDIT CODE ABOVE     #######
        ####### PLANSYS GENERATED CODE:  END  #######
        
        $allowed = ($defaultRule == "allow");
        $roleId = Yii::app()->user->roleId;
        $userId = Yii::app()->user->id;
        
        if (in_array($roleId, $rolesRule["deny"]))  { 
            $allowed = false; 
        }
        
        if (in_array($roleId, $rolesRule["allow"])) { 
            $allowed = true; 
        }
        if (array_key_exists($roleId, $rolesRule["custom"])) { 
            call_user_func($rolesRule["custom"][$roleId], $controller, $action); 
        }
        if (in_array($userId, $usersRule["deny"]))  { 
            $allowed = false;
        }
        if (in_array($userId, $usersRule["allow"])) { 
            $allowed = true;
        }
        if (array_key_exists($userId, $usersRule["custom"])) { 
            call_user_func($usersRule["custom"][$userId], $controller, $action); 
        }
        
        if (!$allowed) {
            throw new CHttpException(403);
        }
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
            $this->accessControl($controller, $action);
            if (Yii::app()->user->isGuest) {
                throw new CHttpException(403);
            }

            return true;
        } else
            return false;
    }

}