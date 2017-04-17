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
                            'ng-show' => 'module == \'dev\'',
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
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"!isNewRecord && module == \'dev\'\\" class=\\"separator\\"></div>',
                    ),
                    array (
                        'label' => 'Hapus',
                        'buttonType' => 'danger',
                        'options' => array (
                            'href' => 'url:/dev/user/del?id={model.id}',
                            'ng-if' => '!isNewRecord && module == \'dev\'',
                            'prompt' => 'Ketik \'DELETE\' (tanpa kutip) untuk menghapus user ini',
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
                        'type' => 'Text',
                        'value' => '<div ng-show=\\"module == \'dev\'\\">',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div class=\"form-group form-group-sm\">   
    <label class=\"control-label col-sm-4\"> 
        User Role(s)
    </label>
    <div class=\"col-sm-8\">',
                    ),
                    array (
                        'name' => 'userRoles',
                        'fieldTemplate' => 'form',
                        'templateForm' => 'application.modules.dev.forms.users.user.DevUserRoleList',
                        'options' => array (
                            'ng-change' => 'updateRole()',
                        ),
                        'singleViewOption' => array (
                            'name' => 'val',
                            'fieldType' => 'text',
                            'labelWidth' => 0,
                            'fieldWidth' => 12,
                            'fieldOptions' => array (
                                'ng-delay' => 500,
                            ),
                        ),
                        'type' => 'ListView',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '    </div>
</div>
<div class=\"clearfix\"></div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div style=\"float:right;margin:-25px 0px 0px 0px;padding:0px;text-align:right;color:#999;font-size:12px;\">
      <i class=\"fa fa-info-circle\"></i> 
     Geser role ke atas 
         untuk menjadikan default
</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '</div>
<div ng-if=\"module != \'dev\'\">',
                    ),
                    array (
                        'type' => 'Text',
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
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '    <div class=\"form-group form-group-sm\">
        <label 
        class=\"col-sm-4 control-label\">
        </label>
        <div class=\"col-sm-8\" 
           style=\"padding-top:10px;\">
            
           <table class=\"table\" style=\"font-size:12px;border:1px solid #ccc;\">
               <tr>
                   <th style=\"padding:2px 5px 0px 5px;background:#ececeb;\">Role</th>
               </tr>
               <tr ng-repeat=\"ur in model.roles\">
                   <td style=\"padding:2px 5px 0px 5px;\">{{ ur.role_description }}</td>
               </tr>
           </table>
        </div>
    </div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '
</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Email',
                        'name' => 'email',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Last Login',
                        'name' => 'last_login',
                        'labelWidth' => '2',
                        'type' => 'LabelField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"module == \'dev\' && !isNewRecord\\">',
                    ),
                    array (
                        'label' => 'LDAP User',
                        'js' => '\'Yes - Synced\'',
                        'labelWidth' => '2',
                        'options' => array (
                            'ng-if' => 'model.useLdap && model.password == \'\'',
                        ),
                        'type' => 'LabelField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '</div>',
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
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                    'where' => 'dataFilter1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(*) from (select count(1) from p_audit_trail where user_id = :id {AND [where]} group by action, model, user_id, model_id, stamp) a',
                'relationTo' => 'auditTrail',
                'relationCriteria' => array (
                    'select' => '|t|.*',
                    'distinct' => 'false',
                    'alias' => 't',
                    'condition' => '{[where]}',
                    'order' => '{[order], |id| desc}',
                    'paging' => '{[paging]}',
                    'group' => '',
                    'having' => '',
                    'join' => '',
                ),
                'type' => 'DataSource',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\"!params.ldap && (!model.useLdap || (model.useLdap && model.password != \'\'))\">
',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div class=\"info\" ng-if=\"!isNewRecord\"><i class=\"fa fa-info-circle fa-nm fa-fw\"></i>&nbsp; 
Isi field disamping untuk mengubah password. 
<br/>Jika tidak ingin dirubah, kosongkan saja.
</div>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"!isNewRecord && params.auditTrailEnabled\\">',
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
                        'label' => 'Date',
                        'filterType' => 'date',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                        'defaultOperator' => 'Daily',
                        'show' => false,
                    ),
                    array (
                        'name' => 'type',
                        'label' => 'Type',
                        'listExpr' => 'AuditTrail::typeDropdown()',
                        'filterType' => 'check',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'show' => false,
                        'queryOperator' => 'in',
                        'list' => array (
                            'general' => array (
                                'view' => 'View',
                                'create' => 'Create',
                                'update' => 'Update',
                                'delete' => 'Delete',
                            ),
                            'other' => array (
                                'login' => 'Login',
                                'logout' => 'Logout',
                                'other' => 'Other',
                            ),
                        ),
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
                        'show' => false,
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
                'options' => array (
                    'freeze' => 'true',
                ),
                'type' => 'DataFilter',
            ),
            array (
                'type' => 'GridView',
                'name' => 'gridView1',
                'label' => 'GridView',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'stamp',
                        'label' => 'Date / Time',
                        'html' => '<td ng-class=\"rowClass(row, \'stamp\', \'string\')\" >
    <div  ng-include=\'\"row-state-template\"\'></div>
    <span class=\'row-group-padding\' ng-if=\'!!row.$level\'
        style=\'width:{{row.$level*10}}px;\'></span>
    {{row.stamp}}
</td>',
                        'columnType' => 'string',
                        '$listViewName' => 'columns',
                        'show' => false,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'default',
                        'options' => array (
                            'mode' => 'datetime',
                            'width' => '150',
                            'freeze' => 'true',
                        ),
                        'headers' => array (
                            'r1' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                            'r2' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                        ),
                    ),
                    array (
                        'name' => 'type',
                        'label' => 'Type',
                        'html' => '<td ng-class=\"rowClass(row, \'type\', \'string\')\" >
    <div ng-if=\"row.type == \'view\'\" class=\'label label-default text-center\' style=\'display:block;width:100%;\'> VIEW </div>
    <div ng-if=\"row.type == \'login\'\" class=\'label label-info text-center\' style=\'display:block;width:100%;\'> LOGIN</div>
    <div ng-if=\"row.type == \'logout\'\" class=\'label label-warning text-center\' style=\'display:block;width:100%;\'> LOGOUT</div>
    <div ng-if=\"row.type == \'update\'\" class=\'label label-primary text-center\' style=\'display:block;width:100%;\'> UPDATE </div>
    <div ng-if=\"row.type == \'create\'\" class=\'label label-success text-center\' style=\'display:block;width:100%;\'> CREATE </div>
    <div ng-if=\"row.type == \'delete\'\" class=\'label label-danger text-center\' style=\'display:block;width:100%;\'> DELETE </div>
</td>',
                        'columnType' => 'string',
                        '$listViewName' => 'columns',
                        'show' => true,
                        'headers' => array (
                            'r1' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                            'r2' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                        ),
                        'mergeSameRow' => 'No',
                        'cellMode' => 'custom',
                        'options' => array (
                            'width' => '75',
                        ),
                    ),
                    array (
                        'name' => 'description',
                        'label' => 'Description',
                        'html' => '<td ng-class=\"rowClass(row, \'description\', \'string\')\" >
    <a ng-url=\"/sys/auditTrail/detail&id={{row.id}}\">{{row.description}}</a>
</td>',
                        'columnType' => 'string',
                        '$listViewName' => 'columns',
                        'show' => true,
                        'headers' => array (
                            'r1' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                            'r2' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                        ),
                        'mergeSameRow' => 'No',
                        'cellMode' => 'custom',
                    ),
                    array (
                        'name' => 'pathinfo',
                        'label' => 'Path',
                        'html' => '<td ng-class=\"rowClass(row, \'pathinfo\', \'string\')\" >
    {{row.pathinfo}}
</td>',
                        'columnType' => 'string',
                        '$listViewName' => 'columns',
                        'show' => true,
                        'headers' => array (
                            'r1' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                            'r2' => array (
                                'colSpan' => 1,
                                'label' => '',
                            ),
                        ),
                        'mergeSameRow' => 'No',
                        'cellMode' => 'default',
                        'options' => array (
                            'width' => '200',
                        ),
                    ),
                ),
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
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
            $this->password = Helper::hash($this->changePassword);
        }
    }

    public function getForm() {
        return array (
            'title' => 'Detail User',
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
        parent::beforeSave();

        $p = $this->getAttributes();
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