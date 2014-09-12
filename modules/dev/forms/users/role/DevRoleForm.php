<?php

class DevRoleForm extends Role {
    
    public function getForm() {
        return array (
            'title' => 'Role',
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
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'type' => 'ColumnField',
                'column1' => array (
                    array (
                        'name' => 'role_name',
                        'type' => 'TextField',
                        'label' => 'Role Name',
                    ),
                    array (
                        'name' => 'role_description',
                        'type' => 'TextField',
                        'label' => 'Role Description',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'name' => 'parent_id',
                        'type' => 'TextField',
                        'label' => 'Parent Id',
                    ),
                    array (
                        'name' => 'userRoles',
                        'type' => 'TextField',
                        'label' => 'User Roles',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
            ),
        );
    }

}