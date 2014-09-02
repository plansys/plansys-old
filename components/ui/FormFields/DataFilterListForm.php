<?php

class DataFilterListForm extends Form {
    public function getFields() {
        return array (
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
                    'list' => 'List',
                    'checkbox' => 'Checkbox',
                    'date' => 'Date',
                ),
                'labelWidth' => '2',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<hr style=\\"margin:0px -4px 6px -4px\\" />',
                'type' => 'Text',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Name',
                        'name' => 'name',
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
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Label',
                        'name' => 'label',
                        'options' => array (
                            'ng-model' => 'value[$index].label',
                            'ng-change' => 'updateListView()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<hr ng-if=\\"value[$index].filterType == \\\'list\\\' || value[$index].filterType == \\\'checkbox\\\'\\" style=\\"margin:-4px -4px 5px -4px\\" />',
                'type' => 'Text',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' => array (
                    'ng-model' => 'value[$index].listExpr',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'value[$index].filterType ==                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                \'list\' || value[$index].filterType ==                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 \'checkbox\'',
                    'style' => 'margin-bottom:8px;',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<div style=\\"margin-bottom:-8px;\\"></div>',
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
