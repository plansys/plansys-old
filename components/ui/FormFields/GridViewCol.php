<?php

class GridViewCol extends Form {

    public $name             = '';
    public $label            = '';
    public $options          = [];
    public $mergeSameRow     = '';
    public $mergeSameRowWith = '';
    public $html             = '';
    public $columnType       = 'string';
    public $typeOptions      = [
        'string' => ['html'],
    ];

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"label data-filter-name pull-right\">
{{value[$index].columnType}}</div>
{{value[$index].label}}
<div class=\"clearfix\"></div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-if=\"value[$index].show\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\'value[$index].show\'>',
            ),
            array (
                'label' => 'Type',
                'name' => 'columnType',
                'options' => array (
                    'ng-model' => 'value[$index].columnType',
                    'ng-change' => 'updateListView()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'string' => 'String',
                    'checkbox' => 'Checkbox',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Col. Name',
                'name' => 'name',
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
                'name' => 'TypeString',
                'subForm' => 'application.components.ui.FormFields.GridViewColString',
                'options' => array (
                    'ng-if' => 'item.columnType == \'string\'',
                ),
                'type' => 'SubForm',
            ),
            array (
                'name' => 'TypeCheckbox',
                'subForm' => 'application.components.ui.FormFields.GridViewColCheckbox',
                'options' => array (
                    'ng-if' => 'item.columnType == \'checkbox\'',
                ),
                'type' => 'SubForm',
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
                'type' => 'Text',
                'value' => '<div style=\'margin-bottom:-3px;\'></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

    ### columnType

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