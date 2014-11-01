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
                        'options' => array (
                            'ng-show' => 'module == \\\'dev\\\'',
                            'href' => 'url:/dev/user/index',
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
                'title' => '{{ form.title}}',
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
                        'value' => '<div ng-show=\\"module == \\\'dev\\\'\\">',
                        'type' => 'Text',
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
                        'templateForm' => 'application.modules.Dev.forms.users.user.DevUserRoleList',
                        'options' => array (
                            'ng-change' => 'updateRole()',
                            'ps-after-add' => 'value.role_id = 1',
                        ),
                        'type' => 'ListView',
                    ),
                    array (
                        'value' => '<div class=\"col-sm-6\" 
     style=\"float:right;margin:-25px 0px 0px 0px;padding:0px;text-align:right;color:#999;font-size:12px;\">
      <i class=\"fa fa-info-circle\"></i> 
     Geser role ke atas 
         untuk menjadikan default
</div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '</div>
<div ng-if=\"module != \'dev\'\">',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<div class=\"form-group form-group-sm\">
    <label 
    class=\"col-sm-4 control-label\">
    Username 
    </label>
    <div class=\"col-sm-6\" 
       style=\"padding-top:5px;\">
       {{ model.username }}
    </div>
</div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<div class=\"form-group form-group-sm\">
    <label 
    class=\"col-sm-4 control-label\">
    Roles 
    </label>
    <div class=\"col-sm-6\" 
       style=\"padding-top:5px;\">
       <span 
       class=\"badge\"
       ng-repeat=\"ur in model.roles\">
       {{ ur.role_description }}
       </span>
    </div>
</div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<div style=\"margin:-50px -20px 0px 0px;\" class=\"hide col-sm-5 pull-right info\">
<i 
class=\"fa fa-info-circle fa-nm fa-fw\"></i> 
Harap hubungi administrator untuk mengubah username ataupun role.
</div>
</div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'NIK',
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
                    array (
                        'value' => '<div ng-if=\\"module == \\\'dev\\\' && !isNewRecord\\">',
                        'type' => 'Text',
                    ),
                    array (
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'LDAP User',
                        'js' => '\\\'Yes - Synced\\\'',
                        'labelWidth' => '2',
                        'options' => array (
                            'ng-if' => 'model.useLdap && model.password == \\\'\\\'',
                        ),
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Notification',
                        'name' => 'subscribed',
                        'labelWidth' => '2',
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'value' => '</div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '<div ng-if=\"!model.useLdap || (model.useLdap && model.password != \'\')\">
',
                'type' => 'Text',
            ),
            array (
                'title' => '{{ isNewRecord ? \\"\\" : \\"Ubah \\"}} Password',
                'type' => 'SectionHeader',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Password',
                        'name' => 'changePassword',
                        'fieldType' => 'password',
                        'fieldOptions' => array (
                            'autocomplete' => 'off',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Repeat Password',
                        'name' => 'repeatPassword',
                        'fieldType' => 'password',
                        'fieldOptions' => array (
                            'autocomplete' => 'off',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<div class=\"info\" ng-if=\"!isNewRecord\"><i class=\"fa fa-info-circle fa-nm fa-fw\"></i>&nbsp; 
Isi field disamping untuk mengubah password. 
<br/>Jika tidak ingin dirubah, kosongkan saja.
</div>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
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
                'sql' => 'select id,
stamp,
action,
model,
group_concat(field separator \', \') 
   as field,
model_id,
group_concat(old_value separator \', \') 
   as old_value,
group_concat(new_value separator \', \') 
   as new_value,
user_id
from p_audit_trail where user_id = :id {AND [where]} group by action, model, user_id, model_id, stamp  {[order]} {[paging]}',
                'params' => array (
                    ':id' => '$model->id',
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                    'where' => 'dataFilter1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(*) from (select count(1) from p_audit_trail where user_id = :id {AND [where]} group by action, model, user_id, model_id, stamp) a',
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
                            'groups' => '[\\\'model_id\\\']',
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
        if ($this->useLdap) return true;
        
        if ($this->changePassword != '' && $this->repeatPassword != $this->changePassword) {
            $this->addError('changePassword', 'Password tidak cocok.');
            $this->addError('repeatPassword', 'Password tidak cocok.');
        }
        
        if ($this->isNewRecord && $this->changePassword == '') {
            $this->addError('changePassword', 'Password harus diisi.');
        }
        
        if (count($this->errors) == 0 && $this->changePassword != '') {
            $this->password = md5($this->changePassword);
        }
    }

    public function getForm() {
        return array (
            'title' => 'UserForm',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'js/form.js',
            'options' => array (
                'autocomplete' => 'off',
            ),
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