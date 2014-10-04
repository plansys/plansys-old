<?php

class DataGridListFormButton extends Form {


    public function getForm() {
        return array (
            'title' => 'DataGridListFormButton',
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
        return array(
            array(
                'label' => 'Collapsed',
                'name' => 'buttonCollapsed',
                'options' => array(
                    'ng-model' => 'value[$index].buttonCollapsed',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array(
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '3',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'value' => '<hr style=\\"margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\\" />',
                'type' => 'Text',
            ),
            array(
                'name' => 'buttons',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListFormButtonItem',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array(
                    'ng-model' => 'value[$index].buttons',
                    'ng-change' => 'save()',
                ),
                'type' => 'ListView',
            ),
            array(
                'value' => '<hr style=\\"margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\\" /><div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
        );
    }

}