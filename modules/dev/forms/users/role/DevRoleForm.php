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
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Role Description',
                        'name' => 'role_description',
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'label' => 'Parent Role',
                        'name' => 'parent_id',
                        'condition' => 'where parent_id = 0 and id != $model->id {AND [where]}',
                        'includeEmpty' => 'Yes',
                        'emptyValue' => '0',
                        'searchable' => 'Yes',
                        'modelClass' => 'application.models.Role',
                        'idField' => 'id',
                        'labelField' => '{role_description} [{role_name}]',
                        'type' => 'RelationField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'User List',
                'type' => 'SectionHeader',
            ),
            array (
                'name' => 'dataFilter1',
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'type' => 'DataGrid',
            ),
        );
    }

}