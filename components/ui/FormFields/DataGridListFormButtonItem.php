<?php

class DataGridListFormButtonItem extends Form {
    public function getFields() {
        return array (
            array (
                'name' => 'label',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'postfix' => 'Label',
                'options' => array (
                    'ng-model' => 'item.label',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'Label',
                ),
                'type' => 'TextField',
            ),
            array (
                'name' => 'icon',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'postfix' => 'Icon',
                'options' => array (
                    'ng-model' => 'item.icon',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'Icon',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Button Options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'item.options',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'type' => 'KeyValueGrid',
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
    public $label = '';
    public $icon = '';
    public $options = array();
}