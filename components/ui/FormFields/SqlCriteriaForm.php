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
                    'ng-model' => '$parent.value.criteria',
                    'ng-change' => 'update()',
                ),
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Params',
                'name' => 'relParams',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => '$parent.value.params',
                    'ng-change' => 'update()',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

}