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
        return array (
            array (
                'type' => 'Text',
                'value' => '<div ng-bind-html="previewSQL"></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\'alert alert-danger\' style=\'padding:5px;font-size:12px;margin-bottom:10px;\' ng-if=\'isError\'> {{ errorMsg}}</div>',
            ),
            array (
                'label' => 'Criteria',
                'name' => 'relCriteria',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => '$parent.value',
                    'ng-change' => 'update()',
                ),
                'allowDoubleQuote' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}