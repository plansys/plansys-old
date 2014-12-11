<?php

class DevRoleForm extends Role {
    
    public function getForm() {
        return array (
            'title' => 'Role',
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

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Cancel',
                        'url' => '/dev/user/roles',
                        'options' => array (
                            'href' => 'url:/dev/user/roles',
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
                'title' => '{{!isNewRecord ? \\\'Role Detail: \\\' + model.role_name : \\\'New Role\\\'}}',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Role Name',
                        'name' => 'role_name',
                        'autocomplete' => 'rel',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Role Description',
                        'name' => 'role_description',
                        'autocomplete' => 'rel',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Home Page',
                        'name' => 'home_url',
                        'prefix' => 'url:',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Menu Path',
                        'name' => 'menu_path',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '<div ng-if=\\"!isNewRecord\\">',
                'type' => 'Text',
            ),
            array (
                'title' => 'User List',
                'type' => 'SectionHeader',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
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
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select u.* from p_user u inner join p_user_role p on u.id = p.user_id and p.role_id = :id {[where]} {[order]} {[paging]}',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'dataGrid1',
                    'paging' => 'dataGrid1',
                ),
                'relationTo' => 'users',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
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
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'last login',
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
                ),
                'gridOptions' => array (
                    'enablePaging' => 'true',
                    'enableExternalSorting' => 'true',
                    'afterSelectionChange' => 'url:/dev/user/update?id={id}',
                ),
                'type' => 'DataGrid',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}