<?php

class DevUserIndex extends User {
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'New User',
                        'url' => '/dev/user/new',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array (
                            'href' => 'url:/dev/user/new',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
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
                        'name' => 'last_login',
                        'label' => 'last login',
                        'listExpr' => '',
                        'filterType' => 'date',
                        'show' => false,
                    ),
                    array (
                        'name' => 'role',
                        'label' => 'role',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select u.*,r.role_description as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id 
 {where [where]} group by u.id {[order]} {[paging]}',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'dataGrid1',
                    'paging' => 'dataGrid1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(1) as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id 
    {where [where]}',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'options' => array (),
                        'columnType' => 'string',
                        'show' => true,
                        'inputMask' => '',
                    ),
                    array (
                        'name' => 'nip',
                        'label' => 'nip',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'fullname',
                        'label' => 'fullname',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'email',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'phone',
                        'label' => 'phone',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'username',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'password',
                        'label' => 'password',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'last_login',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'role',
                        'label' => 'role',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                ),
                'gridOptions' => array (
                    'useExternalSorting' => 'true',
                    'enablePaging' => 'true',
                    'afterSelectionChange' => 'url:/dev/user/update?id={id}',
                    'enableColumnResize' => 'true',
                ),
                'type' => 'DataGrid',
            ),
            array (
                'name' => 'dataSource2',
                'sql' => 'select * from chartdummy',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'pieChart1',
                'datasource' => 'dataSource2',
                'chartTitle' => 'Pie Title',
                'series' => array (
                    array (
                        'label' => 'Series 1',
                        'value' => '2',
                        'color' => '#750707',
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 2',
                        'value' => '1',
                        'color' => '#57C391',
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 3',
                        'value' => '2',
                        'color' => '#022E77',
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                ),
                'type' => 'ChartPie',
            ),
            array (
                'name' => 'lineChart1',
                'datasource' => 'dataSource2',
                'chartTitle' => 'Line Chart',
                'series' => array (
                    array (
                        'label' => 'No',
                        'value' => array (
                            '1',
                            '2',
                            '3',
                            '4',
                        ),
                        'color' => '#5492DB',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 1',
                        'value' => array (
                            '2',
                            '3',
                            '3',
                            '2',
                        ),
                        'color' => '#23429D',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 2',
                        'value' => array (
                            '1',
                            '2',
                            '1',
                            '3',
                        ),
                        'color' => '#7C63DF',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 3',
                        'value' => array (
                            '2',
                            '1',
                            '4',
                            '6',
                        ),
                        'color' => '#55229E',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                ),
                'tickSeries' => 'No',
                'options' => array (
                    'yAxis.title.text' => 'ini yaxis',
                ),
                'type' => 'ChartLine',
            ),
            array (
                'chartType' => 'column',
                'name' => 'barChart1',
                'datasource' => 'dataSource2',
                'chartTitle' => 'Bar Title',
                'series' => array (
                    array (
                        'label' => 'No',
                        'value' => array (
                            '1',
                            '2',
                            '3',
                            '4',
                        ),
                        'color' => '#179796',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 1',
                        'value' => array (
                            '2',
                            '3',
                            '3',
                            '2',
                        ),
                        'color' => '#F105BC',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 2',
                        'value' => array (
                            '1',
                            '2',
                            '1',
                            '3',
                        ),
                        'color' => '#B15FFA',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 3',
                        'value' => array (
                            '2',
                            '1',
                            '4',
                            '6',
                        ),
                        'color' => '#9291C7',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                ),
                'tickSeries' => 'No',
                'type' => 'ChartBar',
            ),
            array (
                'name' => 'areaChart1',
                'datasource' => 'dataSource2',
                'chartTitle' => 'AreaTitle',
                'series' => array (
                    array (
                        'label' => 'No',
                        'value' => array (
                            '1',
                            '2',
                            '3',
                            '4',
                        ),
                        'color' => '#53E4BB',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 1',
                        'value' => array (
                            '2',
                            '3',
                            '3',
                            '2',
                        ),
                        'color' => '#A55E4A',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 2',
                        'value' => array (
                            '1',
                            '2',
                            '1',
                            '3',
                        ),
                        'color' => '#C259C5',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 3',
                        'value' => array (
                            '2',
                            '1',
                            '4',
                            '6',
                        ),
                        'color' => '#B848E1',
                        'isTick' => NULL,
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                ),
                'tickSeries' => 'No',
                'type' => 'ChartArea',
            ),
        );
    }
    
    public function getForm() {
        return array (
            'title' => 'User List',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'js/index.js',
        );
    }
    
}