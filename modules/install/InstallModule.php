<?php

class InstallModule extends CWebModule {

    public function beforeControllerAction($controller, $action) {
        ####### GENERATED CODE - DO NOT EDIT #######
        $mode = "default";
        $defaultAccess = "deny";
        $rolesRule = array(
            'deny' => array(),
            'allow' => array(),
        );
        $usersRule = array(
            'deny' => array(
                '1',
            ),
            'allow' => array(),
        );
        ####### END OF PLANSYS GENERATED CODE ######

        parent::beforeControllerAction($controller, $action);
        $allowed = ($defaultAccess == 'allow');
        $roleId = Yii::app()->user->roleId;
        $userId = Yii::app()->user->id;
        
        if (in_array($roleId, $rolesRule["deny"]))  { $allowed = false; }
        if (in_array($roleId, $rolesRule["allow"])) { $allowed = true; }
        if (in_array($userId, $usersRule["deny"]))  { $allowed = false; }
        if (in_array($userId, $usersRule["allow"])) { $allowed = true;}

        if (!$allowed) {
            throw new CHttpException(403);
        }
        
        return true;
    }

    public function init() {
        // import the module-level controllers and forms
        $this->setImport(array(
            'application.modules.install.controllers.*',
            'application.modules.install.forms.*'
        ));
    }

}
