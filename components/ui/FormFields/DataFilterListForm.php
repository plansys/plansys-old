<?php

class DataFilterListForm extends Form {
    public function getFields() {
        return array (
            array (
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Type',
                        'name' => 'filterType',
                        'options' => array (
                            'ng-model' => 'value[$index].filterType',
                            'ng-change' => 'updateListView()',
                        ),
                        'list' => array (
                            'string' => 'String',
                            'number' => 'Number',
                            'date' => 'Date',
                        ),
                        'type' => 'DropDownList',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            '<hr style="margin:-5px -4px 6px -4px" />',
            array (
                'column1' => array (
                    array (
                        'label' => 'Name',
                        'name' => 'name',
                        'options' => array (
                            'ng-model' => 'value[$index].name',
                            'ng-change' => 'updateListView()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Label',
                        'name' => 'label',
                        'options' => array (
                            'ng-model' => 'value[$index].label',
                            'ng-change' => 'updateListView()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            '<div style="margin-bottom:-8px;"></div>',
        );
    }
    public function getForm() {
        return array (
            'formTitle' => 'DataFilterListForm',
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
    public $name = '';
    public $label = '';
    public $filterType = 'string';
}
