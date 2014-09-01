<?php

class DevUser extends User {
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
            'title' => 'Dev User',
        );
    }
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Cancel',
                        'buttonType' => 'default',
                        'options' => array (
                            'ng-show' => 'form.canGoBack()',
                            'ng-click' => 'form.goBack()',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'filter',
                'datasource' => 'ds',
                'filters' => array (
                    array (
                        'name' => 'date',
                        'label' => 'Daqwd',
                        'filterType' => 'list',
                        'listExpr' => 'Helper::coba()',
                        'list' => array (
                            '-- ALL --',
                            '---',
                            'coba' => array (
                                'coba_dunk' => 'Coba Dunk',
                                'coba_1' => 'Testing',
                                '---' => '---',
                                'fukiii' => 'fukiii',
                            ),
                            'fuki' => 'test',
                        ),
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'ds',
                'sql' => 'select * from p_user {where [where]}',
                'params' => array (
                    'where' => 'filter',
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
                        'label' => 'Email',
                        'name' => 'email',
                        'type' => 'TextField',
                    ),
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
                ),
                'column2' => array (
                    array (
                        'label' => 'Text Area',
                        'name' => 'textArea1',
                        'type' => 'TextArea',
                    ),
                    array (
                        'label' => 'NIP',
                        'name' => 'nip',
                        'options' => array (
                            'ps-ds-sql' => '\'\'',
                        ),
                        'type' => 'TextField',
                    ),
                    '',
                    array (
                        'label' => 'Drop Down List',
                        'name' => 'dropDown List1',
                        'listExpr' => 'Helper::coba()',
                        'type' => 'DropDownList',
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
