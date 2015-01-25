<?php

class SysAuditTrailIndex extends AuditTrail {

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Kembali',
                        'options' => array (
                            'href' => '{{ dataSource1.data[0].url }}',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '<i class=\\"fa fa-history\\"></i> Audit Trail: {{ params.model.description }}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'filterType' => 'date',
                        'name' => 'stamp',
                        'label' => 'Date',
                        'show' => false,
                        'defaultOperator' => 'Daily',
                        'defaultValue' => '',
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                        'options' => array (
                            'width' => '130px',
                        ),
                    ),
                    array (
                        'filterType' => 'check',
                        'name' => 'type',
                        'label' => 'Type',
                        'show' => false,
                        'defaultValue' => '',
                        'listExpr' => 'AuditTrail::typeDropdown(false)',
                        'list' => array (
                            'view' => 'View',
                            'create' => 'Create',
                            'update' => 'Update',
                            'delete' => 'Delete',
                        ),
                    ),
                    array (
                        'filterType' => 'relation',
                        'relModelClass' => 'application.models.User',
                        'relIdField' => 'id',
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => '',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => '',
                        ),
                        'relLabelField' => 'fullname',
                        'name' => 'user_id',
                        'label' => 'User',
                        'show' => false,
                        'defaultValue' => '',
                        'options' => array (
                            'width' => '150px',
                        ),
                        'list' => array (
                            array (
                                'key' => '1',
                                'value' => 'Developer',
                            ),
                            array (
                                'key' => '3',
                                'value' => 'Ukur',
                            ),
                            array (
                                'key' => '4',
                                'value' => 'OE',
                            ),
                            array (
                                'key' => '5',
                                'value' => 'User Lab',
                            ),
                            array (
                                'key' => '6',
                                'value' => 'User PDE',
                            ),
                            array (
                                'key' => '43',
                                'value' => 'Geo RK Darat',
                            ),
                            array (
                                'key' => '44',
                                'value' => 'Geo Bore Log',
                            ),
                            array (
                                'key' => '45',
                                'value' => 'Geo geofisika',
                            ),
                            array (
                                'key' => '46',
                                'value' => 'Geo Kabid Darat',
                            ),
                            array (
                                'key' => '47',
                                'value' => 'Geo Kabid Laut',
                            ),
                            array (
                                'key' => '48',
                                'value' => 'Geo - RK Laut',
                            ),
                            array (
                                'key' => '49',
                                'value' => 'Geo Taksasi',
                            ),
                            array (
                                'key' => '50',
                                'value' => 'User AEOP',
                            ),
                            array (
                                'key' => '51',
                                'value' => 'K3LH - Cuaca',
                            ),
                            array (
                                'key' => '52',
                                'value' => 'K3LH - Reklamasi',
                            ),
                            array (
                                'key' => '53',
                                'value' => 'POP GT',
                            ),
                            array (
                                'key' => '54',
                                'value' => 'POP Perizinan',
                            ),
                            array (
                                'key' => '55',
                                'value' => 'POP PT',
                            ),
                            array (
                                'key' => '56',
                                'value' => 'ULB',
                            ),
                            array (
                                'key' => '57',
                                'value' => 'User UNMET',
                            ),
                            array (
                                'key' => '58',
                                'value' => 'User UTD',
                            ),
                            array (
                                'key' => '125',
                                'value' => 'Satrio Gahara',
                            ),
                            array (
                                'key' => '126',
                                'value' => 'Angga Widya Yogatama',
                            ),
                            array (
                                'key' => '127',
                                'value' => 'Satyogroho D.A',
                            ),
                            array (
                                'key' => '128',
                                'value' => 'Sofyan Darnis',
                            ),
                        ),
                        'count' => '94',
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'Page Title',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                        'defaultOperator' => '',
                        'typeOptions' => array (
                            'string' => array (
                                'defaultOperator',
                                'defaultValue',
                            ),
                            'number' => array (
                                'defaultOperator',
                                'defaultValue',
                            ),
                            'date' => array (
                                'defaultOperator',
                                'defaultValue',
                                'defaultValueFrom',
                                'defaultValueTo',
                            ),
                            'list' => array (
                                'defaultValue',
                                'listExpr',
                            ),
                            'check' => array (
                                'defaultValue',
                                'listExpr',
                            ),
                            'relation' => array (
                                'defaultValue',
                                'relParams',
                                'relCriteria',
                                'relModelClass',
                                'relIdField',
                                'relLabelField',
                            ),
                        ),
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => '',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => '',
                        ),
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'relIncludeEmpty' => 'No',
                        'relEmptyValue' => 'null',
                        'relEmptyLabel' => '-- NONE --',
                        'show' => true,
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'fieldType' => 'phpsql',
                'php' => 'AuditTrail::indexQuery($params);',
                'params' => array (
                    'where' => 'dataFilter1',
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                ),
                'enablePaging' => 'Yes',
                'pagingPHP' => 'AuditTrail::countQuery($params);',
                'relationCriteria' => array (
                    'select' => '',
                    'distinct' => 'false',
                    'alias' => 't',
                    'condition' => '{[where]} {AND} `key` = :key',
                    'order' => '{id desc, [order]}',
                    'paging' => '{[paging]}',
                    'group' => '',
                    'having' => '',
                    'join' => '',
                ),
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'stamp',
                        'label' => 'Date / Time',
                        'options' => array (
                            'width' => '140',
                        ),
                        'inputMask' => '99/99/9999 99:99',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'columnType' => 'string',
                        'options' => array (
                            'width' => '80',
                        ),
                        'name' => 'type',
                        'label' => 'Type',
                        'show' => false,
                        'inputMask' => '',
                        'stringAlias' => array (
                            'view' => '<div class=\\\'label label-default text-center\\\' style=\\\'display:block;width:100%;\\\'> VIEW </div>',
                            'login' => '<div class=\\\'label label-info text-center\\\' style=\\\'display:block;width:100%;\\\'> LOGIN</div>',
                            'logout' => '<div class=\\\'label label-warning text-center\\\' style=\\\'display:block;width:100%;\\\'> LOGOUT</div>',
                            'update' => '<div class=\\\'label label-primary text-center\\\' style=\\\'display:block;width:100%;\\\'> UPDATE </div>',
                            'create' => '<div class=\\\'label label-success text-center\\\' style=\\\'display:block;width:100%;\\\'> CREATE </div>',
                            'delete' => '<div class=\\\'label label-danger text-center\\\' style=\\\'display:block;width:100%;\\\'> DELETE </div>',
                        ),
                    ),
                    array (
                        'relModelClass' => 'application.models.User',
                        'relIdField' => 'id',
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => '',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => '',
                        ),
                        'relLabelField' => 'fullname',
                        'columnType' => 'relation',
                        'name' => 'user_id',
                        'label' => 'User',
                        'show' => false,
                        'options' => array (
                            'width' => '100px',
                        ),
                    ),
                    array (
                        'columnType' => 'string',
                        'options' => array (
                            'href' => 'url:/sys/auditTrail/detail?id={id}',
                            'target' => '_blank',
                        ),
                        'name' => 'description',
                        'label' => 'Page Title',
                        'show' => false,
                        'inputMask' => '',
                        'stringAlias' => array (),
                    ),
                ),
                'gridOptions' => array (
                    'enablePaging' => 'true',
                    'useExternalSorting' => 'true',
                ),
                'type' => 'DataGrid',
            ),
        );
    }

    public function getForm() {
        return array (
            'title' => 'Daftar Audit Trail ',
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

}