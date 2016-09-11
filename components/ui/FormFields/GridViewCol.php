<?php

class GridViewCol extends Form {

    public $name             = '';
    public $label            = '';
    public $options          = [];
    public $mergeSameRow     = '';
    public $mergeSameRowWith = '';
    public $mergeSameRowMethod = 'Default';
    public $html             = '';
    public $columnType       = 'string';
    public $typeOptions      = [
        'string' => ['html'],
    ];

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div ng-init=\"value[$index].$showDF = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"selectDataField(value, $index)\">
<div class=\"label data-filter-name pull-right\">
{{value[$index].columnType}}</div>
{{value[$index].label || value[$index].name }}

<div class=\"label label-default\" style=\'font-weight:normal\' ng-if=\"value[$index].options.mode\">
    {{ value[$index].options.mode }}
</div>
<div class=\"clearfix\"></div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-if=\"value[$index].$showDF\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\'value[$index].$showDF\'>',
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
                    'ng-model' => 'item.name',
                    'ng-if' => 'value[$index].columnType != \'buttons\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'fieldOptions' => array (
                    'class' => 'list-view-item-text',
                    'ng-change' => 'formatColName(item)',
                    'ng-delay' => '500',
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
                'options' => array (
                    'ng-model' => 'value[$index].options',
                    'ng-change' => 'updateListView()',
                ),
                'show' => 'Show',
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