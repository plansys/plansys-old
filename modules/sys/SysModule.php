<?php

class SysModule extends CWebModule {

    public function accessControl($controller,$action) {
        ####### PLANSYS GENERATED CODE: START #######
        $accessType = "DEFAULT";
        $defaultRule = "deny";
        $rolesRule = [
            "deny" => [],
            "allow" => []
        ];
        $usersRule = [
            "deny" => [],
            "allow" => []
        ];
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
        if (in_array($userId, $usersRule["deny"]))  { 
            $allowed = false; 
        }
        if (in_array($userId, $usersRule["allow"])) { 
            $allowed = true;
        }
        
        if (!$allowed) {
            throw new CHttpException(403);
        }
    }

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
                throw new CHttpException(403);
            }

            return true;
        } else
            return false;
    }

}