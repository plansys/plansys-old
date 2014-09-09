<?php

class DevUserRoleList extends UserRole  {
    public function getFields() {
        return array (
            array (
                'name' => '0',
                'options' => array (
                    'ng-model' => 'value[$index].role_id',
                    'ng-change' => 'updateListView()',
                    'style' => 'margin:-5px -20px;',
                ),
                'listExpr' => 'Role::listRole()',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
        );
    }
    public function getForm() {
        return array (
            'title' => 'UserRoleList',
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
    
}