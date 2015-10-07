<?php

class DataGridListFormRelation extends Form {
    
    /** @var string $name */
    public $relParams = [];
    public $relCriteria = [
        'select' => '',
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[search]}',
        'order' => '',
        'group' => '',
        'having' => '',
        'join' => ''
    ];
    public $relModelClass = '';
    public $relIdField = '';
    public $relLabelField = '';
    
    
    public function getForm() {
        return  [
            'title' => 'DataGridListFormRelation',
            'layout' =>  [
                'name' => 'full-width',
                'data' =>  [
                    'col1' =>  [
                        'type' => 'mainform',
                    ],
                ],
            ],
        ];
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Model',
                'name' => 'relModelClass',
                'options' => array (
                    'ng-model' => '$parent.value[$parent.$index].relModelClass',
                    'ng-change' => 'generateRelationField(value, $parent.value[$index]);updateListView();',
                    'ng-init' => 'generateRelationField(value[$index].relModelClass);',
                ),
                'menuPos' => 'pull-right',
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
                'name' => 'relCriteria',
                'label' => 'Sql Criteria',
                'paramsField' => 'relParams',
                'baseClass' => 'DataGrid',
                'options' => array (
                    'ng-change' => 'save();',
                    'ng-model' => 'value[$index].relCriteria',
                ),
                'modelClassJS' => 'DataGrid/inlinejs/relation-criteria.js',
                'type' => 'SqlCriteria',
            ),
            array (
                'label' => 'Sql Parameter',
                'name' => 'relParams',
                'show' => 'Show',
                'options' => array (
                    'ng-change' => 'updateListView();',
                    'ng-model' => 'item.relParams;',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

}