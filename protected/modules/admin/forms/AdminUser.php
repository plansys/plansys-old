<?php

class AdminUser extends User {

    public function getFields() {
        return array (
            array (
                'label' => 'Roles',
                'name' => 'roles',
                'type' => 'TextField',
            ),
            array (
                'totalColumns' => '1',
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column3' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column4' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column5' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Section Header',
                'type' => 'SectionHeader',
            ),
            array (
                'label' => 'Password',
                'name' => 'password',
                'type' => 'TextField',
            ),
            array (
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Username',
                        'name' => 'id',
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Submit',
                'type' => 'SubmitButton',
            ),
        );
    }

    public function getForm() {
        return array (
            'formTitle' => 'User',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'size' => '100',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
            'actions' => array (
                '/admin/user/create',
                '/admin/user/test',
            ),
        );
    }

}
