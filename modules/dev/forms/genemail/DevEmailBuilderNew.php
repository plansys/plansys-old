<?php

class DevEmailBuilderNew extends Form {

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
            'inlineJS' => 'popUpEmail.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (),
                'title' => 'New Email Builder',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'label' => 'Email Template Name',
                'name' => 'templateName',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Save',
                'buttonType' => 'success',
                'buttonSize' => 'btn-sm',
                'buttonPosition' => 'right',
                'type' => 'SubmitButton',
            ),
        );
    }

}