<?php
                            
class DevGenNewCtrl extends Form {

    public function getForm() {
        return array (
            'title' => 'Generate New Controller',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'newCtrl.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save',
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
                'type' => 'Text',
                'value' => '<div style=\'height:15px\'></div>',
            ),
            array (
                'name' => 'module',
                'type' => 'HiddenField',
            ),
            array (
                'label' => 'Controller Name',
                'name' => 'ctrlName',
                'postfix' => 'Controller',
                'options' => array (
                    'ng-change' => 'nameChange()',
                ),
                'fieldOptions' => array (
                    'style' => 'text-align:right',
                ),
                'type' => 'TextField',
            ),
        );
    }

}