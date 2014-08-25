<?php

class AdminUser extends User {
    public function getFields() {
        return array (
            array (
                'type' => 'ActionBar',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Date',
                        'name' => 'date',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Email',
                        'name' => 'email',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Phone',
                        'name' => 'phone',
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'qwdqwd',
                        'name' => 'Lainnya',
                        'fieldType' => 'password',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Firstname',
                        'name' => 'firstname',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'password',
                        'type' => 'TextField',
                    ),
                    array (
                        'name' => 'id',
                        'type' => 'HiddenField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Section Header',
                'type' => 'SectionHeader',
            ),
            array (
                'label' => 'Text Area',
                'name' => 'Text Area 1',
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Lastname',
                'name' => 'lastname',
                'options' => array (
                    'class' => 'col-sm-6',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Lastname',
                'name' => 'test',
                'options' => array (
                    'class' => 'col-sm-6',
                ),
                'type' => 'TextField',
            ),
            '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>',
            array (
                'title' => 'Mantab',
                'type' => 'SectionHeader',
            ),
            '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>',
        );
    }
    public function getForm() {
        return array (
            'title' => 'User',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'options' => array (),
            'includeJS' => array (),
            'inlineJS' => 'user/user.js',
        );
    }
}
