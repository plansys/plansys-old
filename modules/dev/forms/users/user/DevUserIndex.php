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
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'role_description',
                        'label' => 'role',
                        'filterType' => 'relation',
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
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'username',
                        'filterType' => 'string',
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        '$showDF' => false,
                        'isCustom' => 'No',
                        'resetable' => 'Yes',
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'last login',
                        'filterType' => 'date',
                        'defaultOperator' => 'Between',
                        'defaultValue' => 'js: today',
                        'defaultValueFrom' => 'js: today',
                        'defaultValueTo' => 'js: today',
                        '$showDF' => false,
                        'isCustom' => 'No',
                        'resetable' => 'Yes',
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'email',
                        'filterType' => 'string',
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        '$showDF' => false,
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
                'pagingSQL' => 'select count(1) from (select * from (select u.*,r.role_description as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id
 {where [where]}) a) b',
                'relationTo' => 'currentModel',
                'relationCriteria' => array (
                    'select' => '|u|.*, |r.role_description|',
                    'distinct' => 'false',
                    'alias' => 'u',
                    'condition' => '(|is_deleted| = 0 {OR} |is_deleted| is null) {AND [where]}',
                    'order' => '{[order]}',
                    'paging' => '{[paging]}',
                    'group' => '',
                    'having' => '',
                    'join' => 'inner join 
   |p_user_role| |p| on |u.id| = |p.user_id| and |p.is_default_role| = \'Yes\' 
 left outer join 
   |p_role| |r| on |r.id| = |p.role_id|',
                ),
                'type' => 'DataSource',
            ),
            array (
                'type' => 'GridView',
                'name' => 'gridView1',
                'label' => 'GridView',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'id',
                        'label' => '#',
                        'options' => array (),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'mergeSameRowMethod' => 'Default',
                        'html' => '',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        'show' => false,
                        'cellMode' => 'default',
                        '$listViewName' => 'columns',
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'Username',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'Email',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'Last Login',
                        'html' => '<td ng-class=\"rowClass(row, \'last_login\', \'string\')\" >
    {{row.last_login}}
</td>',
                        'columnType' => 'string',
                        'show' => true,
                        '$listViewName' => 'columns',
                        'mergeSameRow' => 'No',
                        'cellMode' => 'default',
                        'options' => array (
                            'mode' => 'datetime',
                        ),
                    ),
                    array (
                        'name' => 'role_description',
                        'label' => 'Role',
                        'html' => '<td ng-class=\"rowClass(row, \'role_description\', \'string\')\" >
    {{row.role_description}}
</td>',
                        'columnType' => 'string',
                        'show' => false,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'default',
                        '$listViewName' => 'columns',
                    ),
                    array (
                        'name' => 'edit',
                        'label' => '',
                        'options' => array (
                            'mode' => 'edit-button',
                            'editUrl' => '/dev/user/update&id={{row.id}}',
                        ),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'mergeSameRowMethod' => 'Default',
                        'html' => '',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        'show' => false,
                        'cellMode' => 'default',
                        '$listViewName' => 'columns',
                    ),
                ),
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