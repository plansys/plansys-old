<?php

class DataGridListFormButton extends Form {
    public function getFields() {
        return array (
            array (
                'name' => 'url',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'value[$index].url',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'Url',
                ),
                'type' => 'TextArea',
            ),
            array (
                'name' => 'icon',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'postfix' => 'Icon',
                'options' => array (
                    'ng-model' => 'value[$index].icon',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'Icon',
                ),
                'type' => 'TextField',
            ),
        );
    }
    
    public function getForm() {
        return array (
            'formTitle' => 'DataFilterListFormButton',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }
    public $url = '';
    public $icon = '';
}
