<?php

class DataGridListFormDropdown extends Form {
    
    public $listType = 'php';
    public $listExpr = '';
    public $listMustChoose = 'No';
    
    public function getForm() {
        return array (
            'title' => 'DataGridListFormDropdown',
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
                'label' => 'List Type',
                'name' => 'listType',
                'options' => array (
                    'ng-model' => 'value[$index].listType',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'php' => 'PHP Function',
                    'js' => 'JS Function',
                ),
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Must Choose',
                'name' => 'listMustChoose',
                'options' => array (
                    'ng-model' => 'value[$index].listMustChoose',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\');',
                'labelWidth' => '8',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'language' => 'html',
                'options' => array (
                    'ng-model' => 'value[$index].listExpr',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'ExpressionField',
            ),
        );
    }

}