<?php

class DataGridListFormDropdown extends Form {
    
    public $listType = 'php';
    public $listExpr = '';
    public $listMustChoose = 'No';
    
    public function getForm() {
        return  [
            'title' => 'DataGridListFormDropdown',
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
                'label' => 'List Type',
                'name' => 'listType',
                'options' =>  [
                    'ng-model' => 'value[$index].listType',
                    'ng-change' => 'updateListView()',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'list' =>  [
                    'php' => 'PHP Function',
                    'js' => 'JS Function',
                ],
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Must Choose',
                'name' => 'listMustChoose',
                'options' =>  [
                    'ng-model' => 'value[$index].listMustChoose',
                    'ng-change' => 'updateListView()',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'listExpr' => 'array(\'Yes\',\'No\');',
                'labelWidth' => '8',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'language' => 'html',
                'options' =>  [
                    'ng-model' => 'value[$index].listExpr',
                    'ng-change' => 'updateListView()',
                ],
                'type' => 'ExpressionField',
            ],
        ];
    }

}