<?php

class DevRoleIndex extends Form {
     
    public function getFields() {
        return array (
            array (
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select p.role_name as parent_role,r.id as id, r.role_name, r.role_description from p_role r left outer join p_role p on r.parent_id = p.id {where [where]} {order by parent_role asc [order]} {[paging]}',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'dataGrid1',
                    'paging' => 'dataGrid1',
                ),
                'debugSql' => 'Yes',
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(1) from p_role r left outer join p_role p on r.parent_id = p.id {where [where]}',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'name' => 'role_name',
                        'label' => 'Role',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'role_description',
                        'label' => 'Description',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'parent_role',
                        'label' => 'Parent Role',
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
                        'name' => 'parent_role',
                        'label' => 'Parent Role',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'columnType' => 'string',
                        'show' => true,
                    ),
                    array (
                        'name' => 'role_name',
                        'label' => 'Role',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'role_description',
                        'label' => 'Description',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'columnType' => 'string',
                        'show' => true,
                    ),
                ),
                'gridOptions' => array (
                    'afterSelectionChange' => 'url:/dev/user/role?id={id}',
                    'useExternalSorting' => 'true',
                    'enablePaging' => 'true',
                ),
                'type' => 'DataGrid',
            ),
        );
    }

    public function getForm() {
        return array (
            'title' => 'Role Manager',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
        );
    }

}