<?php

class DevUserForm extends User {

    public $changePassword = '';
    public $repeatPassword = '';
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Kembali',
                        'url' => '/dev/user/index',
                        'options' => array (
                            'ng-show' => 'module == \\\'dev\\\'',
                            'href' => 'url:/dev/user/{backUrl}',
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
                        'value' => '<div ng-if=\\"!isNewRecord && module == \\\'dev\\\'\\" class=\\"separator\\"></div>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Hapus',
                        'buttonType' => 'danger',
                        'options' => array (
                            'href' => 'url:/dev/user/del?id={model.id}',
                            'ng-if' => '!isNewRecord && module == \\\'dev\\\'',
                            'prompt' => 'Ketik \\\'DELETE\\\' (tanpa kutip) untuk menghapus user ini',
                            'prompt-if' => 'DELETE',
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
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Fullname',
                        'name' => 'fullname',
                        'acMode' => 'comma',
                        'modelClass' => 'application.models.User',
                        'idField' => 'fullname',
                        'labelField' => 'fullname',
                        'acPHP' => '[\\\'JOS\\\',\\\'ASDasDAS\\\',\\\'asdsa\\\']',
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
                        'value' => '    <div class=\"form-group form-group-sm\">
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
                        'value' => '    <div class=\"form-group form-group-sm\">
        <label 
        class=\"col-sm-4 control-label\">
        </label>
        <div class=\"col-sm-8\" 
           style=\"padding-top:10px;\">
            
           <table class=\"table\" style=\"font-size:12px;border:1px solid #ccc;\">
               <tr>
                   <th style=\"padding:2px 5px 0px 5px;background:#ececeb;\">Role</th>
                   <th style=\"padding:2px 5px 0px 5px;background:#ececeb;text-align:center;width:100px\">Notification</th>
               </tr>
               <tr ng-repeat=\"ur in model.roles\">
                   <td style=\"padding:2px 5px 0px 5px;\">{{ ur.role_description }}</td>
                   <td style=\"padding:0px;text-align:center;\">
                       <input name=\"DevUserForm[subscriptionCategories][]\" value=\"{{ur.role_name}}\" type=\"checkbox\" ng-checked=\"ur.subscribed\">
                   </td>
               </tr>
           </table>
        </div>
    </div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '
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
                'value' => '<div ng-if=\"!params.ldap && (!model.useLdap || (model.useLdap && model.password != \'\'))\">
',
                'type' => 'Text',
            ),
            array (
                'title' => '{{ isNewRecord ? \\"\\" : \\"Ubah \\"}} Password',
                'type' => 'SectionHeader',
            ),
            array (
                'showBorder' => 'Yes',
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
                        'label' => 'Date / Time',
                        'filterType' => 'date',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                        'defaultOperator' => '',
                        'show' => false,
                    ),
                    array (
                        'name' => 'type',
                        'label' => 'Type',
                        'listExpr' => '[
\'view\' => \'View\',
\'create\' => \'Create\',
\'update\' => \'Update\',
\'delete\' => \'Delete\',
\'other\' => \'Other\' 
]',
                        'filterType' => 'check',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'show' => true,
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'Description',
                        'filterType' => 'string',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'defaultOperator' => '',
                        'show' => true,
                    ),
                    array (
                        'name' => 'pathinfo',
                        'label' => 'Pathinfo',
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
                'relationTo' => 'auditTrail',
                'relationCriteria' => array (
                    'select' => '',
                    'distinct' => 'false',
                    'alias' => 't',
                    'condition' => '{user_id = :id} {AND} {[where]}',
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
                        'name' => 'type',
                        'label' => 'type',
                        'options' => array (
                            'width' => '80',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array (
                            'view' => '<div class=\\\'label label-default text-center\\\' style=\\\'display:block;width:100%;\\\'> VIEW </div>',
                        ),
                        'columnType' => 'string',
                        'show' => true,
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'description',
                        'options' => array (
                            'href' => '{pathinfo}?{params}',
                            'target' => '_blank',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'pathinfo',
                        'label' => 'path',
                        'options' => array (
                            'width' => '200',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array (),
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
        $p = $this->getAttributes(true, true);
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