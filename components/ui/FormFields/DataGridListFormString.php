<?php

class DataGridListFormString extends Form {

    public $inputMask = '';
    public $stringAlias = '';

    public function getForm() {
        return [
            'title' => 'DataGridListFormString',
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

    public function getFields() {
        return  [
             [
                'label' => 'Input Mask',
                'name' => 'inputMask',
                'options' =>  [
                    'ng-change' => 'updateListView();',
                    'ng-model' => 'item.inputMask',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'list' =>  [
                    '' => '-- NONE --',
                    '--' => '---',
                    '99/99/9999 99:99' => 'Date Time',
                    '99/99/9999' => 'Date',
                    '99:99' => 'Time',
                    'number' => 'Number',
                ],
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'String Alias',
                'name' => 'stringAlias',
                'options' =>  [
                    'ng-model' => 'item.stringAlias;',
                    'ng-change' => 'updateListView();',
                ],
                'type' => 'KeyValueGrid',
            ],
        ];
    }

}