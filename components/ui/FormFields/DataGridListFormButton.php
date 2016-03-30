<?php

class DataGridListFormButton extends Form {


    public function getForm() {
        return  [
            'title' => 'DataGridListFormButton',
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
                'label' => 'Collapsed',
                'name' => 'buttonCollapsed',
                'options' => array (
                    'ng-model' => '$parent.$parent.value[$parent.$parent.$index].buttonCollapsed',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'labelWidth' => '3',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-show=\'!!showButtonProp\' style=\'margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\' />',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"btn btn-default btn-sm\" ng-show=\'!showButtonProp\' style=\'margin-top:-35px;float:right;\' ng-click=\"showButtonProp = true\">Show Buttons <i class=\"fa fa-chevron-down\"></i></div>
<div class=\"btn btn-default btn-sm\" ng-show=\'!!showButtonProp\' style=\'margin-top:-43px;float:right;\' ng-click=\"showButtonProp = false\">Hide Buttons <i class=\"fa fa-chevron-up\"></i></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-show=\'!!showButtonProp\'>',
            ),
            array (
                'name' => 'buttons',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListFormButtonItem',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => '$parent.$parent.value[$parent.$parent.$index].buttons',
                    'ng-change' => 'save()',
                ),
                'singleViewOption' => array (
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array (
                        'ng-delay' => 500,
                    ),
                ),
                'type' => 'ListView',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr style=\'margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\' /><div class=\'clearfix\'></div>',
            ),
        );
    }

}