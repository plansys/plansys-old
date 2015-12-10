<?php

class DevEmailBuilderNew extends Form {

    public $module;

    public function getForm() {
        return array (
            'title' => 'Email Builder',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'size' => '100',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
            'inlineJS' => 'DevEmailBuilderNew.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'buttonSize' => 'btn-sm',
                        'buttonPosition' => 'right',
                        'type' => 'SubmitButton',
                    ),
                ),
                'title' => 'New Email Template',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'label' => 'Template Name',
                'name' => 'templateName',
                'options' => array (
                    'style' => 'margin-top:15px',
                    'ng-change' => 'model.templateName = formatClass(model.templateName)',
                ),
                'type' => 'TextField',
            ),
            array (
                'name' => 'module',
                'type' => 'HiddenField',
            ),
        );
    }

}