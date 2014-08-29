<?php

class DevUser extends User {
    public function getForm() {
        return array (
            'formTitle' => 'User',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'title' => 'Dev User',
        );
    }
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Cancel',
                        'buttonType' => 'default',
                        'options' => array (
                            'ng-show' => 'form.canGoBack()',
                            'ng-click' => 'form.goBack()',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'manteb',
                'datasource' => 'dataSoqwd',
                'filters' => array (
                    array (
                        'name' => 'testing',
                        'label' => 'Testing',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'okedeh',
                        'label' => 'Mantab',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                    array (
                        'name' => 'qwrrwq',
                        'label' => 'qwtt',
                        'filterType' => 'string',
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSoqwd',
                'sql' => 'select * from p_user',
                'params' => array (
                    'where' => 'nip',
                ),
                'data' => array (
                    array (
                        'id' => '1',
                        'nip' => '12345',
                        'fullname' => 'Admin',
                        'email' => 'admin@web.com',
                        'phone' => '00000000',
                        'username' => 'admin',
                        'password' => '827ccb0eea8a706c4c34a16891f84e7b',
                        'date' => '0000-00-00',
                    ),
                ),
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
                ),
                'column2' => array (
                    array (
                        'label' => 'NIP',
                        'name' => 'nip',
                        'options' => array (
                            'ps-ds-sql' => '\'\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Drop Down List',
                        'name' => 'dropDown List1',
                        'listExpr' => 'Helper::coba()',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'List View',
                        'name' => 'phone',
                        'fieldTemplate' => 'form',
                        'type' => 'ListView',
                    ),
                    array (
                        'label' => 'Firstna',
                        'options' => array (
                            'ps-sql' => 'name = :mantab',
                            'ps-sql-mantab' => 'model.username',
                            'ps-sql-okedeh' => '',
                        ),
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
            '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>',
        );
    }
    
}
