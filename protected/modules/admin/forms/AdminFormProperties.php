<?php

class AdminFormProperties extends Form {
    public function getForm() {
        return array (
            'formTitle' => 'FormProperties',
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
    public function getFields() {
        return array (
            array (
                'label' => 'Form Title',
                'name' => 'formTitle',
                'options' => array (
                    'ng-model' => 'form.formTitle',
                    'ng-change' => 'saveForm();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Form Layout',
                'name' => 'layout_name',
                'list' => array (
                    'full-width' => 'full-width',
                    '2-cols' => '2-cols',
                    '3-cols' => '3-cols',
                    '2-rows' => '2-rows',
                    'z...' => '...',
                ),
                'listExpr' => 'array(\\\'full-width\\\',\\\'2-cols\\\',\\\'3-cols\\\',\\\'2-rows\\\')',
                'iconTemplate' => '<img src=\\"{base_url}/static/img/columns/{icon}.png\\" />',
                'fieldWidth' => '150',
                'options' => array (
                    'ng-model' => 'form.layout.name',
                    'ng-change' => 'changeLayoutType(form.layout.name)',
                ),
                'type' => 'IconPicker',
            ),
        );
    }
    
}
