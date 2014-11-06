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
        return  [
             [
                'value' => '<Hr/>',
                'type' => 'Text',
            ],
             [
                'label' => 'Model',
                'name' => 'relModelClass',
                'options' =>  [
                    'ng-model' => '$parent.value[$parent.$index].relModelClass',
                    'ng-change' => 'generateRelationField(value, $parent.value[$index]);updateListView();',
                    'ng-init' => 'generateRelationField(value[$index].relModelClass);',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'listExpr' => 'RelationField::listModel()',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'ID Field',
                'name' => 'relIdField',
                'options' =>  [
                    'ng-model' => 'item.relIdField',
                    'ng-change' => 'updateListView();',
                    'ps-list' => 'relationFieldList',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'list' =>  [],
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Label',
                'name' => 'relLabelField',
                'options' =>  [
                    'ng-model' => 'item.relLabelField',
                    'ng-change' => 'updateListView();',
                    'ps-list' => 'relationFieldList',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'list' =>  [],
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ],
             [
                'name' => 'relCriteria',
                'label' => 'Sql Criteria',
                'paramsField' => 'relParams',
                'baseClass' => 'DataGrid',
                'options' =>  [
                    'ng-change' => 'save();',
                    'ng-model' => 'value[$index].relCriteria',
                ],
                'modelClassJS' => 'DataGrid/inlinejs/relation-criteria.js',
                'type' => 'SqlCriteria',
            ],
             [
                'label' => 'Sql Parameter',
                'name' => 'relParams',
                'show' => 'Show',
                'options' =>  [
                    'ng-change' => 'updateListView();',
                    'ng-model' => 'item.relParams;',
                ],
                'type' => 'KeyValueGrid',
            ],
        ];
    }

}