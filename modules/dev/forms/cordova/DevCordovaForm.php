<?php

class DevCordovaForm extends Form {
    public $app, $icon, $package;
    
    
    public function getForm() {
        return array (
            'title' => 'Generate Cordova - Android App',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'form.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Next',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'App Name',
                        'name' => 'app',
                        'fieldOptions' => array (
                            'ng-blur' => 'appBlur()',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Package ID',
                        'name' => 'package',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'name' => 'icon',
                        'label' => 'Upload File',
                        'mode' => 'Upload + Download',
                        'type' => 'UploadFile',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
        );
    }

}