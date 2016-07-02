<?php

class InstallDbForm extends Form {

    public $driver;
    public $host;
    public $username;
    public $password;
    public $dbname;
    public $resetdb = "yes";
    public $phpPath = '';

    public function rules() {
        return [
            ['host, username', 'required']
        ];
    }

    public function getForm() {
        return array(
            'title' => 'Plansys Installer - Database Information',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
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
                'type' => 'Text',
                'value' => '<div class=\"install-pane\" style=\"width:350px;\">
    <div class=\"install-pane-head\">
        <img src=\"<?= Yii::app()->controller->staticUrl(\"/img/logo.png\"); ?>\" alt=\"Logo Plansys\" />
    </div>
    
    <div ng-if=\"!params.error\" style=\"margin-top:15px;\" class=\"alert alert-info\"><?= Setting::t(\"Please enter your database server information \") ?></div>
    
    <div ng-if=\"params.error\" style=\"margin-top:15px;\" class=\"alert alert-danger\">{{params.error}}</div>
    ',
            ),
            array (
                'label' => 'Driver',
                'name' => 'driver',
                'listExpr' => 'Setting::getDBDriverList()',
                'layout' => 'Vertical',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Host:',
                'name' => 'host',
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
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Database name:',
                'name' => 'dbname',
                'layout' => 'Vertical',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'name' => 'resetdb',
                'list' => array (
                    'yes' => 'Create Plansys table',
                ),
                'labelWidth' => '0',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'CheckboxList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"info text-left\" style=\"margin:-20px 0px 0px -3px\">
    only tables with prefix p_ (e.g. p_user, p_role, etc)<br/> that will be created.
</div>

<br/>',
            ),
            array (
                'label' => 'Next Step',
                'buttonType' => 'success',
                'buttonSize' => '',
                'type' => 'SubmitButton',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}