<?php

class TextFieldRelation extends Form {
    
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
                'label' => 'Model',
                'name' => 'relModelClass',
                'options' => array (
                    'ng-model' => 'active.relModelClass',
                    'ng-change' => 'generateRelationField(active.relModelClass);save()',
                    'ng-init' => 'generateRelationField(active.relModelClass);',
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
                'label' => 'Label',
                'name' => 'relLabelField',
                'options' => array (
                    'ng-model' => 'active.relLabelField',
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