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
                'value' => '<pre>{{previewSQL}}</pre>',
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