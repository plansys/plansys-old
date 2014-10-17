<?php

class SqlCriteriaForm extends Form {

    public $relParams = array();
    public $relCriteria = array();

    public function getForm() {
        return array(
            'title' => 'SqlCriteriaForm',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<pre ng-style=\\"{borderColor: (isError ? \\\'red\\\' : \\\'#ccc\\\')}\\">{{previewSQL}}</pre>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div class=\\"alert alert-danger\\" style=\\"padding:5px;font-size:12px;\\" ng-if=\\"isError\\"> {{ errorMsg}}</div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Criteria',
                'name' => 'relCriteria',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => '$parent.value',
                    'ng-change' => 'update()',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

}