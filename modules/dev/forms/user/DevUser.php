<?php

class DevUser extends User {

    public function getForm() {
        return array (
            'title' => 'User',
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
                        'name' => 'nip',
                        'type' => 'TextField',
                        'label' => 'Nip',
                    ),
                    array (
                        'name' => 'fullname',
                        'type' => 'TextField',
                        'label' => 'Fullname',
                    ),
                    array (
                        'name' => 'email',
                        'type' => 'TextField',
                        'label' => 'Email',
                    ),
                    array (
                        'name' => 'phone',
                        'type' => 'TextField',
                        'label' => 'Phone',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'name' => 'username',
                        'type' => 'TextField',
                        'label' => 'Username',
                    ),
                    array (
                        'name' => 'password',
                        'type' => 'TextField',
                        'label' => 'Password',
                    ),
                    array (
                        'name' => 'last_login',
                        'type' => 'TextField',
                        'label' => 'Last Login',
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