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
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'name' => 'nip',
                        'label' => 'nip',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'fullname',
                        'label' => 'fullname',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'email',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'listExpr' => '',
                        'filterType' => 'number',
                        'show' => false,
                    ),
                    array (
                        'name' => 'phone',
                        'label' => 'phone',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'username',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'password',
                        'label' => 'password',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'date',
                        'label' => 'date',
                        'listExpr' => '',
                        'filterType' => 'date',
                        'show' => false,
                    ),
                ),
                'options' => array (
                    'ps-ds-sql' => 'DataFilter::generateParams($paramName, $params)',
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'sort' => 'Yes',
                        'options' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                ),
                'type' => 'DataGrid',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select * from p_user {where [where]}',
                'params' => array (
                    'where' => 'dataFilter1',
                ),
                'type' => 'DataSource',
            ),
            array (
                'column1' => array (
                    array (
                        'name' => 'nip',
                        'fieldWidth' => '8',
                        'uploadPath' => 'tes',
                        'fileType' => 'rar , zip',
                        'type' => 'UploadFile',
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
                        'label' => 'Drop Down List',
                        'name' => 'dropDown List1',
                        'listExpr' => 'Helper::coba()',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'List View',
                        'name' => 'phone',
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
            '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>',
        );
    }
    
}
