<?php

class SqlCriteriaForm extends Form {

    public $relParams = [];
    public $relCriteria = [];

    public function getForm() {
        return [
            'title' => 'SqlCriteriaForm',
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
                'value' => '<pre ng-style=\\"{borderColor: (isError ? \\\'red\\\' : \\\'#ccc\\\')}\\">{{previewSQL}}</pre>',
                'type' => 'Text',
            ],
             [
                'value' => '<div class=\\"alert alert-danger\\" style=\\"padding:5px;font-size:12px;\\" ng-if=\\"isError\\"> {{ errorMsg}}</div>',
                'type' => 'Text',
            ],
             [
                'label' => 'Criteria',
                'name' => 'relCriteria',
                'show' => 'Show',
                'options' =>  [
                    'ng-model' => '$parent.value',
                    'ng-change' => 'update()',
                ],
                'allowDoubleQuote' => 'Yes',
                'type' => 'KeyValueGrid',
            ],
        ];
    }

}