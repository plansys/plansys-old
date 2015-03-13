<?php

class DevGenModuleAccess extends Form {

    public $user;
    public $role;
    public $access;

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
                            'ng-if' => 'name == \\\'rolesRule\\\'',
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
                            'ng-if' => 'name == \\\'usersRule\\\'',
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
                            'deny' => 'Deny',
                            'allow' => 'Allow',
                            'custom' => 'Custom',
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
                'w1' => '25%',
                'w2' => '75%',
                'w3' => '25%',
                'w4' => '25%',
                'w5' => '20%',
                'type' => 'ColumnField',
            ),
        );
    }

}