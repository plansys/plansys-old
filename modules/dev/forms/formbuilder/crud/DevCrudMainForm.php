<?php

class DevCrudMainForm extends Form {

    public $name = '';
    public $path = '';
    public $model = '';
    
    public function rules() {
        return [
            ['name','required']
        ];
    }
    
    public function getForm() {
        return array (
            'title' => 'Generate CRUD',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'crud.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Next Step',
                        'buttonType' => 'success',
                        'icon' => 'caret-right',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Base Model',
                        'name' => 'model',
                        'listExpr' => 'ModelGenerator::listModels(true)',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Name',
                        'name' => 'name',
                        'options' => array (
                            'ng-change' => 'onNameChange()',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Path Alias',
                        'value' => 'Path Alias: {{}}',
                        'js' => 'params.alias + model.name',
                        'fieldOptions' => array (
                            'name' => '',
                        ),
                        'type' => 'LabelField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<input type=\\"hidden\\" name=\\"DevCrudMainForm[path]\\" value=\\"{{params.alias + model.name}}\\">',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'perColumnOptions' => array (
                    'style' => 'padding-right:0px;',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Relations',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<pre>{{ debug | json }}</pre>',
            ),
        );
    }

}