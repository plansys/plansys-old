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
                'label' => 'Fields',
                'name' => 'fields',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'autocomplete' => 'php',
                'acMode' => 'comma',
                'acPHP' => 'ModelGenerator::getFields();',
                'type' => 'TextField',
            ),
            array (
                'column1' => array (
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
                'w1' => '20%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
        );
    }

}