<?php

class DevRoleIndex extends Role {
     
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'New Role',
                        'url' => '/dev/user/newRole',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array (
                            'href' => 'url:/dev/user/newRole',
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
                        'name' => 'role_name',
                        'label' => 'Role',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                    array (
                        'name' => 'role_description',
                        'label' => 'Description',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select * from p_role {[where]} {order by role_name asc, [order]} {[paging]}',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'dataGrid1',
                    'paging' => 'dataGrid1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(1) from p_role {where [where]}',
                'relationTo' => 'currentModel',
                'type' => 'DataSource',
            ),
            array (
                'type' => 'GridView',
                'name' => 'gridView1',
                'label' => 'GridView',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'role_name',
                        'label' => 'Role Name',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'role_description',
                        'label' => 'Role Description',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'menu_path',
                        'label' => 'Menu Path',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'home_url',
                        'label' => 'Home Url',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'edit',
                        'label' => '',
                        'options' => array (
                            'mode' => 'edit-button',
                            'editUrl' => '/dev/user/role&id={{row.id}}',
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
                        'show' => true,
                        'cellMode' => 'default',
                        '$listViewName' => 'columns',
                        '$showDF' => true,
                    ),
                ),
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