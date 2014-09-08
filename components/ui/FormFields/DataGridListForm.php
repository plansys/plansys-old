<?php

class DataGridListForm extends Form {
    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'No',
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"label data-filter-name pull-right\"> 
{{value[$index].columnType}}</div>
{{value[$index].label}} 
<div class=\"clearfix\"></div>
</div>',
                'type' => 'Text',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<hr ng-show=\"value[$index].show\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
                'type' => 'Text',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<div ng-show=\\"value[$index].show\\">',
                'type' => 'Text',
            ),
            array (
                'label' => 'Type',
                'name' => 'columnType',
                'options' => array (
                    'ng-model' => 'value[$index].columnType',
                    'ng-change' => '$parent.changeButtonType(value[$index]);updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'string' => 'String',
                    'buttons' => 'Buttons',
                ),
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Name',
                'name' => 'name',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'value[$index].name',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'value[$index].columnType != \'buttons\'',
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
                'label' => 'Header',
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
                'renderInEditor' => 'No',
                'value' => '<div ng-if=\\"value[$index].columnType == \\\'buttons\\\'\\">',
                'type' => 'Text',
            ),
            array (
                'label' => 'Collapsed',
                'name' => 'buttonCollapsed',
                'options' => array (
                    'ng-model' => 'value[$index].buttonCollapsed',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '3',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<hr style=\\"margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\\" />',
                'type' => 'Text',
            ),
            array (
                'name' => 'buttons',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListFormButton',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'value[$index].buttons',
                    'ng-change' => 'save()',
                ),
                'type' => 'ListView',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<hr style=\\"margin:0px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\\" /><div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '</div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'value[$index].options',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'KeyValueGrid',
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
    public $options = array();
    public $buttonCollapsed = 'Yes';
    public $buttons = array(
        array(
            'label'=>'',
            ''
        )
    );
    public $columnType = 'string';
}
