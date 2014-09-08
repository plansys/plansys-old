<?php

class DevUser extends User {
    public function getFields() {
        return array (
            array (
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select * from test {where [where]} {order by [order]}',
                'php' => 'Helper::coba()',
                'postData' => 'Yes',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'phone',
                ),
                'debugSql' => 'Yes',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'listExpr' => '',
                        'filterType' => 'number',
                        'show' => false,
                    ),
                    array (
                        'name' => 'name',
                        'label' => 'name',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'title',
                        'label' => 'title',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'description',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'value',
                        'label' => 'value',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
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
                        'sort' => 'No',
                        'options' => array (
                            'categoryDisplayName' => 'testing',
                        ),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'description',
                        'sort' => 'Yes',
                        'options' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'name',
                        'label' => 'name',
                        'sort' => 'Yes',
                        'options' => array (
                            'categoryDisplayName' => 'testing',
                        ),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'title',
                        'label' => 'title',
                        'sort' => 'Yes',
                        'options' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'value',
                        'label' => 'value',
                        'sort' => 'Yes',
                        'options' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => '',
                        'label' => '',
                        'options' => array (),
                        'buttons' => array (
                            array (
                                'url' => '#',
                                'icon' => 'fa fa-pencil',
                                'options' => array (
                                    'ajax' => 'true',
                                    'ajax-success' => 'removeRow(row);',
                                    'confirm' => 'Are you sure ?',
                                ),
                            ),
                        ),
                        'columnType' => 'buttons',
                        'show' => false,
                        'buttonCollapsed' => 'Yes',
                    ),
                    array (
                        'name' => '',
                        'label' => 'oke',
                        'options' => array (),
                        'buttonCollapsed' => 'No',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                                'url' => '#',
                                'icon' => 'fa fa-globe',
                                'options' => array (
                                    'ajax' => 'true',
                                    'ajax-success' => 'removeRow(row)',
                                ),
                            ),
                        ),
                        'columnType' => 'buttons',
                        'show' => false,
                    ),
                ),
                'gridOptions' => array (
                    'enableCellSelection' => 'true',
                    'enableRowSelection' => 'false',
                    'enableCellEditOnFocus' => 'true',
                    'enableColumnResize' => 'true',
                    'enableColumnReordering' => 'true',
                    'maintainColumnRatios' => 'true',
                    'enablePaging' => 'true',
                    'showFooter' => 'true',
                ),
                'type' => 'DataGrid',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Nip',
                        'name' => 'nip',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Fullname',
                        'name' => 'fullname',
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
                        'label' => 'Phone',
                        'name' => 'phone',
                        'options' => array (
                            'ps-ds-sql' => 'Helper::test($paramName, $params)',
                            'ng-change' => 'changePhone(model.phone)',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'password',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Date',
                        'name' => 'date',
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
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
            'inlineJS' => 'user/user.js',
            'includeJS' => array (),
        );
    }
}
