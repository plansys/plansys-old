<?php

class DevUserRoleList extends UserRole  {
    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'No',
                'value' => '<div 
ng-if=\"item.is_default_role ==\'Yes\'\"
style=\"position:absolute;top:5px;right:30px;z-index:99;pointer-events:none;\"
class=\"label label-success\">DEFAULT</div>',
                'type' => 'Text',
            ),
            array (
                'name' => 'role_id',
                'options' => array (
                    'ng-model' => 'value[$index].role_id',
                    'ng-change' => 'updateListView();',
                    'style' => 'margin:-5px -20px;',
                ),
                'listExpr' => 'Role::listRole()',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
        );
    }
    public function getForm() {
        return array (
            'title' => 'UserRoleList',
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
    
}