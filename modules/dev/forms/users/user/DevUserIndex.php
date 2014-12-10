<?php

class DevUserIndex extends User {
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Import From LDAP',
                        'icon' => 'user',
                        'options' => array (
                            'ng-if' => 'params.useLdap',
                            'href' => 'url:/dev/user/ldap',
                        ),
                        'type' => 'LinkButton',
                    ),
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
                        'filterType' => 'number',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'nip',
                        'label' => 'nip',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'fullname',
                        'label' => 'fullname',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'email',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'phone',
                        'label' => 'phone',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'username',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'last login',
                        'filterType' => 'date',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                    ),
                    array (
                        'name' => 'role',
                        'label' => 'role',
                        'filterType' => 'relation',
                        'show' => true,
                        'defaultValue' => '',
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
                        'relModelClass' => 'application.models.Role',
                        'relIdField' => 'role_description',
                        'relLabelField' => 'role_description',
                    ),
                ),
                'filterOperators' => array (
                    'string' => array (
                        'Is Any Of',
                        'Is Not Any Of',
                        'Contains',
                        'Does Not Contain',
                        'Is Equal To',
                        'Starts With',
                        'Ends With',
                        'Is Empty',
                    ),
                    'number' => array (
                        '=',
                        '<>',
                        '>',
                        '>=',
                        '<=',
                        '<',
                        'Is Empty',
                    ),
                    'date' => array (
                        'Between',
                        'Not Between',
                        'Less Than',
                        'More Than',
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select * from (select u.*,r.role_description as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id 
) a {where [where]} group by id {[order]} {[paging]}',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'dataGrid1',
                    'paging' => 'dataGrid1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select * from (select count(1) as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id 
 ) a {where [where]}',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'options' => array (
                            'enableCellEdit' => 'false',
                        ),
                        'columnType' => 'string',
                        'show' => false,
                        'inputMask' => '',
                        'stringAlias' => array (),
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
                    array (
                        'name' => 'last_login',
                        'label' => 'last_login',
                        'options' => array (),
                        'columnType' => 'string',
                        'show' => false,
                        'inputMask' => '99/99/9999 99:99',
                        'stringAlias' => array (),
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