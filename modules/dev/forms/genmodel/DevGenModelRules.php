<?php

class DevGenModelRules extends Form {

    public $rule;
    public $fields;
    public $options;

    public function getForm() {
        return array(
            'title'  => 'Gen Model Rules',
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
                'type' => 'TagField',
                'name' => 'fields',
                'label' => 'qweqwe',
                'layout' => 'Horizontal',
            ),
            array (
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Rule',
                        'name' => 'rule',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '70%',
                'w2' => '30%',
                'type' => 'ColumnField',
            ),
        );
    }

}