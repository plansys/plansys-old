<?php

class AdminUser extends User {
    public function getFields() {
        return array (
            '<h2><center>{{ form.formTitle }}</center></h2><hr/>',
            array (
                'label' => 'Nama Lengkap',
                'name' => 'fullname',
                'type' => 'TextField',
            ),
            array (
                'totalColumns' => '3',
                'showBorder' => 'Yes',
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column3' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Password',
                        'name' => 'password',
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'SectionField',
            ),
            array (
                'label' => 'NIP',
                'name' => 'nip',
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Roles',
                'name' => 'roles',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Submit',
                'type' => 'SubmitButton',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
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