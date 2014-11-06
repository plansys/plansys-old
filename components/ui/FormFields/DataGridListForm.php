<?php

class DataGridListForm extends Form {

    public function getFields() {
        return  [
             [
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"label data-filter-name pull-right\"> 
{{value[$index].columnType}}</div>
{{value[$index].label}} 
<div class=\"clearfix\"></div>
</div>',
                'type' => 'Text',
            ],
             [
                'value' => '<hr ng-if=\"value[$index].show\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
                'type' => 'Text',
            ],
             [
                'value' => '<div ng-if=\\"value[$index].show\\">',
                'type' => 'Text',
            ],
             [
                'label' => 'Type',
                'name' => 'columnType',
                'options' =>  [
                    'ng-model' => 'value[$index].columnType',
                    'ng-change' => 'updateListView()',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'list' =>  [
                    'string' => 'String',
                    'buttons' => 'Buttons',
                    'dropdown' => 'Dropdown',
                    'relation' => 'Relation',
                ],
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Name',
                'name' => 'name',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' =>  [
                    'ng-model' => 'value[$index].name',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'value[$index].columnType != \\\'buttons\\\'',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'fieldOptions' =>  [
                    'class' => 'list-view-item-text',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Header',
                'name' => 'label',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' =>  [
                    'ng-model' => 'value[$index].label',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'type' => 'TextField',
            ],
             [
                'name' => 'TypeString',
                'subForm' => 'application.components.ui.FormFields.DataGridListFormString',
                'options' =>  [
                    'ng-if' => 'value[$index].columnType == \\\'string\\\'',
                ],
                'inlineJS' => 'DataGrid/inlinejs/dg-type.js',
                'type' => 'SubForm',
            ],
             [
                'name' => 'TypeDropDown',
                'subForm' => 'application.components.ui.FormFields.DataGridListFormDropdown',
                'options' =>  [
                    'ng-if' => 'value[$index].columnType == \\\'dropdown\\\'',
                ],
                'inlineJS' => 'DataGrid/inlinejs/dg-type.js',
                'type' => 'SubForm',
            ],
             [
                'name' => 'TypeButton',
                'subForm' => 'application.components.ui.FormFields.DataGridListFormButton',
                'options' =>  [
                    'ng-if' => 'value[$index].columnType == \\\'buttons\\\'',
                ],
                'inlineJS' => 'DataGrid/inlinejs/dg-type.js',
                'type' => 'SubForm',
            ],
             [
                'name' => 'TypeRelation',
                'subForm' => 'application.components.ui.FormFields.DataGridListFormRelation',
                'options' =>  [
                    'ng-if' => 'value[$index].columnType == \\\'relation\\\'',
                ],
                'inlineJS' => 'DataGrid/inlinejs/dg-type.js',
                'type' => 'SubForm',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'options' =>  [
                    'ng-model' => 'value[$index].options',
                    'ng-change' => 'updateListView()',
                ],
                'type' => 'KeyValueGrid',
            ],
             [
                'value' => '<div style=\\"margin-bottom:-3px;\\"></div>',
                'type' => 'Text',
            ],
             [
                'value' => '</div>',
                'type' => 'Text',
            ],
        ];
    }

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

    public $name = '';
    public $label = '';
    public $options = [];
    public $typeOptions = [
        'string' => ['inputMask', 'options', 'stringAlias'],
        'buttons' => ['buttonCollapsed', 'buttons', 'options'],
        'dropdown' => ['listType', 'listExpr', 'listMustChoose', 'options'],
        'relation' => ['relParams', 'relCriteria', 'relModelClass', 'relIdField', 'relLabelField', 'options']
    ];

    ### string options
    public $inputMask = '';
    public $stringAlias = [];

    ### button Options
    public $buttonCollapsed = 'Yes';
    public $buttons = null;

    ### dropdown Options
    public $listType = 'php';
    public $listExpr = '';
    public $listMustChoose = 'No';

    ### relation Options
    public $relParams = [];
    public $relCriteria = [
        'select' => '',
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[search]}',
        'order' => '',
        'group' => '',
        'having' => '',
        'join' => ''
    ];
    public $relModelClass = '';
    public $relIdField = '';
    public $relLabelField = '';

    ### columnType
    public $columnType = 'string';

}