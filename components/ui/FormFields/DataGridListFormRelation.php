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
                'label' => 'Model Class',
                'name' => 'relModelClass',
                'options' => array (
                    'ng-model' => 'value[$index].relModelClass',
                    'ng-change' => 'generateRelationField(value);save();',
                    'ng-init' => 'generateRelationField(value[$index].relModelClass);',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'RelationField::listModel()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'ID Field',
                'name' => 'relIdField',
                'options' => array (
                    'ng-model' => 'value[$index].relIdField',
                    'ng-change' => 'save();',
                    'ps-list' => 'relationFieldList',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label Field',
                'name' => 'relLabelField',
                'options' => array (
                    'ng-model' => 'value[$index].relLabelField',
                    'ng-change' => 'save();',
                    'ps-list' => 'relationFieldList',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (),
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
                    'ng-change' => 'save();',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'SQL Condition',
                ),
                'type' => 'TextArea',
            ),
        );
    }

}