<?php

class DevGenModuleAccess extends Form {

    public $user;
    public $role;
    public $access;
    public $func;
    public $customMode = 'redirect';

    public function getForm() {
        return array (
            'title' => 'Gen Module Access',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'column1' => array (
                    array (
                        'name' => 'role',
                        'options' => array (
                            'ng-if' => 'name == \'rolesRule\'',
                        ),
                        'labelWidth' => '0',
                        'fieldWidth' => '12',
                        'modelClass' => 'application.models.Role',
                        'idField' => 'id',
                        'labelField' => 'role_name',
                        'type' => 'RelationField',
                    ),
                    array (
                        'name' => 'user',
                        'options' => array (
                            'ng-if' => 'name == \'usersRule\'',
                        ),
                        'labelWidth' => '0',
                        'fieldWidth' => '12',
                        'modelClass' => 'application.models.User',
                        'idField' => 'id',
                        'labelField' => 'username',
                        'type' => 'RelationField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'name' => 'access',
                        'defaultType' => 'first',
                        'list' => array (
                            'allow' => 'Allow',
                            'deny' => 'Deny',
                            '---' => '---',
                            'custom' => 'Custom Action',
                        ),
                        'labelWidth' => '0',
                        'fieldWidth' => '12',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '75%',
                'w2' => '25%',
                'w3' => '25%',
                'w4' => '25%',
                'w5' => '20%',
                'perColumnOptions' => array (
                    'style' => 'padding:5px;',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\"model.access == \'custom\'\" style=\'margin:0px 0px 5px 0px;\'>

    <div ng-if=\"model.customMode == \'custom\'\" style=\'float:right;margin:6px 0px 0px 0px;text-align:right;\'>
        Variable: 
        <span class=\"badge badge-default\">$controller</span>
        <span class=\"badge badge-default\">$action</span>
        <span class=\"badge badge-default\">$this</span>
    </div>',
            ),
            array (
                'name' => 'customMode',
                'list' => array (
                    'redirect' => 'Redirect',
                    'custom' => 'Custom Code',
                ),
                'labelWidth' => '0',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '
    <div ng-if=\"model.customMode == \'custom\'\" class=\"custom-editor\"
     style=\"width:100%;
     height:100px;border-radius:4px;\"
     ng-model=\"model.func\"
     ui-ace=\"aceConfig({inline:true, advanced:{showPrintMargin: false}})\"></div>
</div>',
            ),
        );
    }

}