<?php

class DevUserRoleList extends Role  {
    public function getFields() {
        return array (
            array (
                'name' => 'id',
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