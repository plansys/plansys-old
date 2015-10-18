<?php

class DataFilterListForm extends Form {

    public $name             = '';
    public $label            = '';
    public $listExpr         = '';
    public $filterType       = 'string';
    public $isCustom         = 'No';
    public $options          = [];
    public $resetable        = 'Yes';
    public $defaultValue     = '';
    public $defaultValueFrom = '';
    public $defaultValueTo   = '';
    public $defaultOperator  = '';
    public $showOther        = 'No';
    public $otherLabel       = '';
    public $typeOptions      = [
        'string' => ['defaultOperator', 'defaultValue'],
        'number' => ['defaultOperator', 'defaultValue'],
        'date' => ['defaultOperator', 'defaultValue', 'defaultValueFrom', 'defaultValueTo'],
        'list' => ['defaultValue', 'listExpr'],
        'check' => ['defaultValue', 'listExpr'],
        'relation' => ['defaultValue', 'relParams', 'relCriteria', 'relModelClass', 'relIdField', 'relLabelField'],
    ];
    public $relParams        = [];
    public $relCriteria      = [
        'select' => '',
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[search]}',
        'order' => '',
        'group' => '',
        'having' => '',
        'join' => ''
    ];

    ### Relation ###
    public $relModelClass   = '';
    public $relIdField      = '';
    public $relLabelField   = '';
    public $relIncludeEmpty = 'No';
    public $relEmptyValue   = 'null';
    public $relEmptyLabel   = '-- NONE --';
    public $queryOperator   = "";

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"label data-filter-name pull-right\"> {{value[$index].filterType}}</div>

{{value[$index].label}}
<div class=\"clearfix\"></div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-show=\"value[$index].show\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\'value[$index].show\'>',
            ),
            array (
                'label' => 'Type',
                'name' => 'filterType',
                'options' => array (
                    'ng-model' => 'value[$index].filterType',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'string' => 'String',
                    'number' => 'Number',
                    'date' => 'Date',
                    'list' => 'DropDownList',
                    'check' => 'Checkbox',
                    'relation' => 'Relation',
                ),
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Col. Name',
                'name' => 'name',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'value[$index].name',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'fieldOptions' => array (
                    'class' => 'list-view-item-text',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'value[$index].label',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Dropdown Behavior',
                'name' => 'isCustom',
                'options' => array (
                    'ng-model' => 'value[$index].isCustom',
                    'ng-change' => 'updateListView()',
                ),
                'menuPos' => 'pull-right',
                'defaultType' => 'first',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'No' => 'Default',
                    'Yes' => 'Custom',
                ),
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Reset-able',
                'name' => 'resetable',
                'options' => array (
                    'ng-model' => 'value[$index].resetable',
                    'ng-change' => 'updateListView()',
                ),
                'menuPos' => 'pull-right',
                'defaultType' => 'first',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\'Yes\',\'No\');',
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Query Operator',
                'name' => 'queryOperator',
                'options' => array (
                    'ng-model' => 'value[$index].queryOperator',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.filterType == \'check\' && item.filterType == \'list\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => '[\'\'=>\'Like\', \'in\'=>\'In\']',
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"margin-top:15px;\">
    <hr/>
    <div style=\"background:white;margin-top:-19px;color:#aaa;padding:5px 5px 5px 3px;width:50px;margin-left:-4px;\">Default</div>
</div>',
            ),
            array (
                'label' => 'Operator',
                'name' => 'defaultOperator',
                'options' => array (
                    'ng-model' => 'value[$index].defaultOperator',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.filterType == \'number\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'DataFilter::getFilterOperators(\'number\')',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Operator',
                'name' => 'defaultOperator',
                'options' => array (
                    'ng-model' => 'value[$index].defaultOperator',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.filterType == \'string\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'DataFilter::getFilterOperators(\'string\')',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Operator',
                'name' => 'defaultOperator',
                'options' => array (
                    'ng-model' => 'value[$index].defaultOperator',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.filterType == \'date\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'DataFilter::getFilterOperators(\'date\')',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Value',
                'name' => 'defaultValue',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'value[$index].defaultValue',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.defaultOperator != \'Between\' && item.defaultOperator != \'Not Between\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'From',
                'name' => 'defaultValueFrom',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'value[$index].defaultValueFrom',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.defaultOperator != \'\' && (item.filterType == \'date\' && (item.defaultOperator == \'Between\' || item.defaultOperator == \'Not Between\'))',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'To',
                'name' => 'defaultValueTo',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'value[$index].defaultValueTo',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.defaultOperator != \'\' && (item.filterType == \'date\' && (item.defaultOperator == \'Between\' || item.defaultOperator == \'Not Between\'))',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'value[$index].options',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' => array (
                    'ng-model' => 'value[$index].listExpr',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'value[$index].filterType ==\'list\' || value[$index].filterType == \'check\'',
                    'style' => 'margin-bottom:8px;',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'name' => 'TypeRelation',
                'subForm' => 'application.components.ui.FormFields.DataFilterListFormRelation',
                'options' => array (
                    'ng-if' => 'value[$index].filterType == \'relation\'',
                ),
                'inlineJS' => 'DataFilter/inlinejs/dfr-init.js',
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\'margin-bottom:-3px;\'></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

    public function getForm() {
        return [
            'formTitle' => 'DataFilterListForm',
            'layout' => [
                'name' => 'full-width',
                'data' => [
                    'col1' => [
                        'type' => 'mainform',
                    ],
                ],
            ],
        ];
    }
}