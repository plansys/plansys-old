<?php

class DataFilterListFormRelation extends Form {
    
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
    public $relIncludeEmpty = 'No';
    public $relEmptyValue = 'null';
    public $relEmptyLabel = '-- NONE --';
    
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
                'value' => '<Hr/>',
            ),
            array (
                'label' => 'Model',
                'name' => 'relModelClass',
                'options' => array (
                    'ng-model' => 'item.relModelClass',
                    'ng-change' => 'generateRelationField(value, $parent.value[$index]);updateListView();',
                    'ng-init' => 'generateRelationField(item.relModelClass);',
                    'ng-if' => 'item.filterType == \'relation\'',
                ),
                'menuPos' => 'pull-right',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'RelationField::listModel(true)',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-show=\\"!!item.relModelClass\\">',
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
                'defaultType' => 'first',
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
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Include Empty Result',
                'name' => 'relIncludeEmpty',
                'options' => array (
                    'ng-model' => 'item.relIncludeEmpty',
                    'ng-change' => 'updateListView();',
                ),
                'menuPos' => 'pull-right',
                'defaultType' => 'first',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => '[\'No\',\'Yes\']',
                'labelWidth' => '8',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Empty Value',
                'name' => 'relEmptyValue',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'item.relEmptyValue',
                    'ng-change' => 'updateListView();',
                    'ng-delay' => '500',
                    'ng-if' => 'item.relIncludeEmpty == \'Yes\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Empty Label',
                'name' => 'relEmptyLabel',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'item.relEmptyLabel',
                    'ng-change' => 'updateListView();',
                    'ng-delay' => '500',
                    'ng-if' => 'item.relIncludeEmpty == \'Yes\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'name' => 'relCriteria',
                'label' => 'Sql Criteria',
                'paramsField' => 'relParams',
                'baseClass' => 'DataFilter',
                'options' => array (
                    'ng-change' => 'save();',
                    'ng-model' => 'item.relCriteria',
                ),
                'modelClassJS' => 'DataFilter/inlinejs/relation-criteria.js',
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
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}