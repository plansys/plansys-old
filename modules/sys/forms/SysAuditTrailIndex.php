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
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'Page Title',
                        'filterType' => 'string',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'defaultOperator' => '',
                        'show' => false,
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