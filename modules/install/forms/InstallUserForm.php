<?php

class InstallUserForm extends Form {
    public $username;
    public $fullname;
    public $password;
    
    public function rules() {
        return [
            ['fullname, username, password', 'required']
        ];
    }
    
    public function getForm() {
        return array (
            'title' => 'Plansys Installer - Database Information',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<div class=\"install-pane\" style=\"width:350px;\">
    <div class=\"install-pane-head\">
        <img src=\"<?= Yii::app()->controller->staticUrl(\"/img/logo.png\"); ?>\" alt=\"Logo Plansys\" />
    </div>
    
    <div ng-if=\"!params.error\" style=\"margin-top:15px;\" class=\"alert alert-info\"><?= Setting::t(\"Please enter new developer account information\") ?></div>
    
    <div ng-if=\"params.error\" style=\"margin-top:15px;\" class=\"alert alert-danger\">{{params.error}}</div>
    ',
                'type' => 'Text',
            ),
            array (
                'label' => 'Fullname:',
                'name' => 'fullname',
                'layout' => 'Vertical',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Username:',
                'name' => 'username',
                'layout' => 'Vertical',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Password:',
                'name' => 'password',
                'fieldType' => 'password',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'value' => '<br/>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Finish Installation',
                'buttonType' => 'success',
                'buttonSize' => '',
                'type' => 'SubmitButton',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}