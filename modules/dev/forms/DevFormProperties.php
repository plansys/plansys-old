<?php

class DevFormProperties extends Form {
    public $title;
    public $layoutName;
    public $options = array();
    public $inlineScript = "";
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
                'dej',
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
                'type' => 'TextField',
            ),
            array (
                'label' => 'Form Layout',
                'name' => 'layoutName',
                'listExpr' => 'array(\\\'full-width\\\',\\\'2-cols\\\',\\\'3-cols\\\',\\\'2-rows\\\')',
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
                'name' => 'inlineScript',
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
                ),
                'type' => 'ListView',
            ),
            array (
                'label' => 'Form Options',
                'fieldname' => 'options',
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
