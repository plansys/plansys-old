<?php

class DevFormProperties extends Form {

    public $title;
    public $layoutName;
    public $options = array();
    public $inlineJS = "";
    public $includeJS = array();
    public $includeCSS = array();

    public function getForm() {
        return array (
            'title' => 'FormProperties',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'includeJS' => array (
                'asfga',
                'asf',
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'label' => 'Form Title',
                'name' => 'title',
                'options' => array (
                    'ng-model' => '$parent.form.title',
                    'ng-change' => 'saveForm();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Form Layout',
                'name' => 'layoutName',
                'listExpr' => 'Layout::listLayout()',
                'iconTemplate' => '<img src=\\"{plansys_url}/static/img/columns/{icon}.png\\" />',
                'fieldWidth' => '150',
                'options' => array (
                    'ng-model' => '$parent.form.layout.name',
                    'ng-change' => 'changeLayoutType(form.layout.name)',
                ),
                'type' => 'IconPicker',
            ),
            array (
                'label' => 'Inline JS File',
                'name' => 'inlineJS',
                'options' => array (
                    'ng-model' => '$parent.form.inlineJS',
                    'ng-change' => 'saveForm();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Include JS File',
                'name' => 'includeJS',
                'options' => array (
                    'ng-model' => '$parent.form.includeJS',
                    'ng-change' => 'saveForm()',
                    'class' => 'flat',
                    'unique' => 'true',
                ),
                'singleView' => 'DropDownList',
                'singleViewOption' => array (
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array (
                        'ng-delay' => 500,
                    ),
                    'list' => array (),
                ),
                'type' => 'ListView',
            ),
            array (
                'label' => 'Form Options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => '$parent.form.options',
                    'ng-change' => 'saveForm()',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }
}