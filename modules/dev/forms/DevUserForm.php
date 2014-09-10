<?php

class DevUserForm extends User {

    public $changePassword = '';
    public $repeatPassword = '';

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Cancel',
                        'url' => '/dev/user/index',
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
                'title' => '{{!isNewRecord ? \\\'Update User: \\\' + model.username                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                : \\\'New User\\\'}}',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Fullname',
                        'name' => 'fullname',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'User Role',
                        'name' => 'userRoles',
                        'fieldTemplate' => 'form',
                        'templateForm' => 'application.modules.dev.forms.DevUserRoleList',
                        'options' => array (
                            'ng-change' => 'updateRole()',
                            'ps-after-add' => 'value.role_id = 1',
                        ),
                        'type' => 'ListView',
                    ),
                    '<div style="float:right;margin-top:-25px;text-align:right;color:#999;font-size:12px;"><i class="fa fa-info-circle"></i> Drag role to the first order to make it default</div>',
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'label' => 'NIP',
                        'name' => 'nip',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Phone',
                        'name' => 'phone',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Email',
                        'name' => 'email',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => '{{ isNewRecord ? \\"\\" : \\"Change\\"}} Password',
                'type' => 'SectionHeader',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Password',
                        'name' => 'password',
                        'fieldType' => 'password',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Repeat Password',
                        'name' => 'repeatPassword',
                        'fieldType' => 'password',
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Audit Trail',
                'type' => 'SectionHeader',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'name' => 'stamp',
                        'label' => 'Timestamp',
                        'listExpr' => '',
                        'filterType' => 'date',
                        'show' => false,
                    ),
                    array (
                        'name' => 'action',
                        'label' => 'action',
                        'listExpr' => 'array(
                                                        \'CREATE\'=>\'CREATE\',
                                                        \'CHANGE\'=>\'CHANGE\'
                                                        )',
                        'filterType' => 'list',
                        'show' => true,
                        'list' => array (
                            'CREATE' => 'CREATE',
                            'CHANGE' => 'CHANGE',
                        ),
                    ),
                    array (
                        'name' => 'model',
                        'label' => 'model',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'model_id',
                        'label' => 'model_id',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'field',
                        'label' => 'field',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'old_value',
                        'label' => 'old_value',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'new_value',
                        'label' => 'new_value',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select * from p_audit_trail where user_id = :id {AND [where]} {[order]} {[paging]}',
                'params' => array (
                    ':id' => '$model->id',
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                    'where' => 'dataFilter1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(1) from p_audit_trail where user_id = :id {AND [where]}',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'stamp',
                        'label' => 'Timestamp',
                        'options' => array (
                            'groups' => '[\'model_id\']',
                        ),
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
                        'name' => 'action',
                        'label' => 'action',
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
                        'name' => 'model',
                        'label' => 'model',
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
                        'name' => 'model_id',
                        'label' => 'model_id',
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
                        'name' => 'field',
                        'label' => 'field',
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
                        'name' => 'old_value',
                        'label' => 'old_value',
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
                        'name' => 'new_value',
                        'label' => 'new_value',
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
                    'enableRowSelection' => 'false',
                    'useExternalSorting' => 'true',
                ),
                'type' => 'DataGrid',
            ),
        );
    }

    public function rules() {
        $rules = array(
            array('changePassword, repeatPassword', 'editPassword')
        );

        return array_merge($rules, parent::rules());
    }

    public function editPassword() {
        if ($this->changePassword != '' && $this->repeatPassword != $this->changePassword) {
            $this->addError('changePassword', 'Password tidak cocok.');
            $this->addError('repeatPassword', 'Password tidak cocok.');
        }
        
        if ($this->isNewRecord && $this->changePassword == '') {
            $this->addError('changePassword', 'Password harus diisi.');
        }
        
        if (count($this->errors) == 0) {
            $this->password = md5($this->changePassword);
        }
    }

    public function getForm() {
        return array(
            'title' => 'UserForm',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'user/index.js',
        );
    }

    public function beforeSave() {
        $p = $this->attributes;
        $p['userRoles'] = Helper::uniqueArray($p['userRoles'], 'role_id');

        
        foreach ($p['userRoles'] as $k => $v) {
            if ($k == 0) {
                $p['userRoles'][$k]['is_default_role'] = 'Yes';
            } else {
                $p['userRoles'][$k]['is_default_role'] = 'No';
            }
        }
        return true;
    }

}
