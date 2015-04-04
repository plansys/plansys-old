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
                'column1' => array (
                    array (
                        'type' => 'TagField',
                        'name' => 'fields',
                        'dropdown' => 'normal',
                        'fieldWidth' => '12',
                        'options' => array (
                            'ng-change' => 'saveRules()',
                        ),
                        'fieldOptions' => array (
                            'disabled' => 'disabled',
                        ),
                        'drPHP' => 'ModelGenerator::getFields($model->parent->name);',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'name' => 'rule',
                        'listExpr' => 'ModelGenerator::getRuleList()',
                        'layout' => 'Vertical',
                        'fieldWidth' => '12',
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