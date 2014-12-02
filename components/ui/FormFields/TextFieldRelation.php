<?php

class TextFieldRelation extends Form {
    
    /** @var string $name */
    public $params = [];
    public $criteria = [
        'select' => '',
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[search]}',
        'order' => '',
        'group' => '',
        'having' => '',
        'join' => ''
    ];
    public $modelClass = '';
    public $idField = '';
    public $labelField = '';
    
    
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
                'name' => 'modelClass',
                'options' => array (
                    'ng-model' => 'active.modelClass',
                    'ng-change' => 'generateRelationField(active.modelClass);save()',
                    'ng-init' => 'generateRelationField(active.modelClass);',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => 'RelationField::listModel()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'ID Field',
                'name' => 'idField',
                'options' => array (
                    'ng-model' => 'active.idField',
                    'ng-change' => 'save();',
                    'ps-list' => 'relationFieldList',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'labelField',
                'options' => array (
                    'ng-model' => 'active.labelField',
                    'ng-change' => 'save();',
                    'ps-list' => 'relationFieldList',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ),
            array (
                'name' => 'criteria',
                'label' => 'Sql Criteria',
                'paramsField' => 'params',
                'baseClass' => 'TextField',
                'options' => array (
                    'ng-change' => 'save();',
                    'ng-model' => 'active.criteria',
                ),
                'modelClassJS' => 'TextField/inlinejs/relation-criteria.js',
                'type' => 'SqlCriteria',
            ),
            array (
                'label' => 'Sql Parameter',
                'name' => 'params',
                'show' => 'Show',
                'options' => array (
                    'ng-change' => 'save();',
                    'ng-model' => 'active.params;',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

}