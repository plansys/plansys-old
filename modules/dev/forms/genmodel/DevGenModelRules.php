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
                'type' => 'Text',
                'value' => '<div class=\"alert alert-info\">
    INI RULES
</div>',
            ),
        );
    }

}