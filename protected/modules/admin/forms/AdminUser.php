<?php

class AdminUser extends User {
    public function getFields() {
        return array (
            array (
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select * from p_user',
                'type' => 'DataSource',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Date',
                        'name' => 'date',
                        'fieldWidth' => 8,
                        'type' => 'DateTimePicker',
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
                        'label' => 'NIP',
                        'name' => 'nip',
                        'fieldType' => 'password',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Firstna',
                        'name' => 'whoadahodqwhoi',
                        'fieldType' => 'password',
                        'options' => array (
                            'ps-sql' => 'name = :mantab',
                            'ps-sql-mantab' => 'model.username',
                            'ps-sql-okedeh' => '',
                        ),
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
                'name' => 'TextArea1',
                'fieldHeight' => '0',
                'options' => array (
                    'class' => 'col-sm-6',
                ),
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
