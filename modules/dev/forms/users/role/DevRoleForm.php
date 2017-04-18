<?php

class DevRoleForm extends Role {
    public $module;
    public function getForm() {
        return array (
            'title' => 'Role',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'DevRoleForm.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Kembali',
                        'url' => '/dev/user/roles',
                        'options' => array (
                            'href' => 'url:/dev/user/roles',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Simpan',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div class=\\"separator\\"></div>',
                    ),
                    array (
                        'label' => 'Hapus',
                        'buttonType' => 'danger',
                        'options' => array (
                            'href' => 'url:/dev/user/roledel?id={model.id}',
                            'ng-if' => '!isNewRecord && module == \'dev\'',
                            'prompt' => 'Ketik \'DELETE\' (tanpa kutip) untuk menghapus role ini',
                            'prompt-if' => 'DELETE',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '{{!isNewRecord ? \'Role Detail: \' + model.role_name : \'New Role\'}}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Module',
                        'name' => 'module',
                        'options' => array (
                            'ng-change' => 'roleNameChange()',
                        ),
                        'listExpr' => '[\'\'=>\'App\',
\'---\' => \'---\'] + ModuleGenerator::listAppModules()',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Role Name',
                        'name' => 'role_name',
                        'options' => array (
                            'ng-change' => 'roleNameChange()',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Role Description',
                        'name' => 'role_description',
                        'fieldOptions' => array (
                            'auto-grow' => 'true',
                        ),
                        'type' => 'TextArea',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Home Page',
                        'name' => 'home_url',
                        'prefix' => 'url:',
                        'options' => array (
                            'ng-if' => '!isNewRecord',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Menu Tree',
                        'name' => 'menu_path',
                        'options' => array (
                            'ng-if' => '!isNewRecord',
                        ),
                        'listExpr' => 'MenuTree::listDropdown($model->rootRole ,\'Default\',true, true);',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Repo Path',
                        'name' => 'repo_path',
                        'prefix' => 'repo/',
                        'options' => array (
                            'ng-if' => '!isNewRecord',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"!isNewRecord\\">',
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
                        'name' => 'username',
                        'label' => 'Username',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        'isCustom' => 'No',
                        'resetable' => 'Yes',
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'Email',
                        'filterType' => 'string',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        'isCustom' => 'No',
                        'resetable' => 'Yes',
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'Last login',
                        'filterType' => 'date',
                        'show' => false,
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                        'isCustom' => 'No',
                        'resetable' => 'Yes',
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
                        'name' => 'id',
                        'label' => 'User ID',
                        'options' => array (),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                        '$showDF' => false,
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
                        '$listViewName' => 'columns',
                        '$showDF' => false,
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
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'default',
                    ),
                    array (
                        'name' => '',
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
                        '$listViewName' => 'columns',
                        '$showDF' => true,
                        'cellMode' => 'default',
                    ),
                ),
                'gridOptions' => array (
                    'enablePaging' => 'true',
                    'enableExternalSorting' => 'true',
                    'afterSelectionChange' => 'url:/dev/user/update?id={id}',
                ),
                'type' => 'GridView',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}