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
        return [
            [
                'label' => 'Collapsed',
                'name' => 'buttonCollapsed',
                'options' => [
                    'ng-model' => 'value[$index].buttonCollapsed',
                    'ng-change' => 'updateListView()',
                ],
                'labelOptions' => [
                    'style' => 'text-align:left;',
                ],
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '3',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
            [
                'value' => '<hr style=\\"margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\\" />',
                'type' => 'Text',
            ],
            [
                'name' => 'buttons',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListFormButtonItem',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => [
                    'ng-model' => 'value[$index].buttons',
                    'ng-change' => 'save()',
                ],
                'type' => 'ListView',
            ],
            [
                'value' => '<hr style=\\"margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\\" /><div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ],
        ];
    }

}