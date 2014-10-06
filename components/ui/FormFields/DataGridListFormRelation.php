<?php

class DataGridListFormRelation extends Form {
    
    /** @var string $name */
    public $relCondition = '';
    public $relModelClass = '';
    public $relIdField = '';
    public $relLabelField = '';
        
    public function getForm() {
        return array (
            'title' => 'DataGridListFormRelation',
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
                'value' => '<Hr/>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Model',
                'name' => 'relModelClass',
                'options' => array (
                    'ng-model' => '$parent.value[$parent.$index].relModelClass',
                    'ng-change' => 'generateRelationField(value, $parent.value[$index]);updateListView();',
                    'ng-init' => 'generateRelationField(value[$index].relModelClass);',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'RelationField::listModel()',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'ID Field',
                'name' => 'relIdField',
                'options' => array (
                    'ng-model' => 'item.relIdField',
                    'ng-change' => 'updateListView();',
                    'ps-list' => 'relationFieldList',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (),
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'relLabelField',
                'options' => array (
                    'ng-model' => 'item.relLabelField',
                    'ng-change' => 'updateListView();',
                    'ps-list' => 'relationFieldList',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (),
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ),
            array (
                'name' => 'relCondition',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'fieldHeight' => '0',
                'options' => array (
                    'ng-model' => 'value[$index].relCondition',
                    'ng-change' => 'updateListView();',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'SQL Condition:

Example: inner join p_user_role p on p_user.id = p.user_id {and p.role_id = [model.role_id]} {where [search]}',
                    'style' => 'min-height:100px;',
                ),
                'type' => 'TextArea',
            ),
        );
    }

}