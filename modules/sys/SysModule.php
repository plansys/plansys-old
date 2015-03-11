<?php

class SysModule extends CWebModule {

    public function init() {
        // import the module-level controllers and forms
        $this->setImport(array(
            'application.modules.sys.controllers.*',
            'application.modules.sys.forms.*'
        ));
    }

    public function beforeControllerAction($controller,$action) {
        ####### GENERATED CODE - DO NOT EDIT #######
        $mode = "DEFAULT";
        $defaultRule = "deny";
        $rolesRule = [
            "deny" => [],
            "allow" => []
        ];
        $usersRule = [
            "deny" => [],
            "allow" => []
        ];
        ####### END OF PLANSYS GENERATED CODE ######
        
        parent::beforeControllerAction($controller, $action);
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
        
        return true;
    }

}