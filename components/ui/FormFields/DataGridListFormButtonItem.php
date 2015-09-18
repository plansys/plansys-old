<?php

class DataGridListFormButtonItem extends Form {
    public function getFields() {
        return  [
             [
                'name' => 'label',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'postfix' => 'Label',
                'options' =>  [
                    'ng-model' => 'item.label',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ],
                'fieldOptions' =>  [
                    'placeholder' => 'Label',
                ],
                'type' => 'TextField',
            ],
             [
                'name' => 'icon',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'postfix' => 'Icon',
                'options' =>  [
                    'ng-model' => 'item.icon',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ],
                'fieldOptions' =>  [
                    'placeholder' => 'Icon',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Button Options',
                'show' => 'Show',
                'options' =>  [
                    'ng-model' => 'item.options',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ],
                'type' => 'KeyValueGrid',
            ],
        ];
    }
    
    public function getForm() {
        return  [
            'formTitle' => 'DataFilterListFormButton',
            'layout' =>  [
                'name' => 'full-width',
                'data' =>  [
                    'col1' =>  [
                        'type' => 'mainform',
                    ],
                ],
            ],
        ];
    }
    public $label = '';
    public $icon = '';
    public $options = [];
}