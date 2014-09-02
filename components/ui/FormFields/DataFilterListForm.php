<?php

class DataFilterListForm extends Form {
    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'No',
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"pull-right\"> {{value[$index].name}}</div>
<div style=\"width:60px;float:left;\">
<div class=\"badge\">{{value[$index].filterType}}</div>
</div>

Label: {{value[$index].label}} 
</div>',
                'type' => 'Text',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<hr ng-show=\"value[$index].show\"
style=\"margin:4px -4px 6px -4px\" />',
                'type' => 'Text',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<div ng-show=\\"value[$index].show\\">',
                'type' => 'Text',
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
                    'list' => 'List',
                    'check' => 'Checkbox',
                ),
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Name',
                'name' => 'name',
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'options' => array (
                    'ng-model' => 'value[$index].name',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '2',
                'fieldWidth' => '10',
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
                'renderInEditor' => 'No',
                'value' => '<div style=\\"margin-bottom:-3px;\\"></div>',
                'type' => 'Text',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }
    
    public function getForm() {
        return array (
            'formTitle' => 'DataFilterListForm',
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
    public $name = '';
    public $label = '';
    public $listExpr = '';
    public $filterType = 'string';
}
