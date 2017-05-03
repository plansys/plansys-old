<?php

class DocsModule extends WebModule {
 
    public function accessControl($controller,$action) {
        ####### PLANSYS GENERATED CODE: START #######
        #######    DO NOT EDIT CODE BELOW     #######
        $accessType = "DEFAULT";
        $defaultRule = "allow";
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
        $this->setImport(array(
            'application.modules.docs.controllers.*',
            'application.modules.docs.forms.*'
        ));
    }

}