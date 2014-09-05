<?php
class DevControllerEditor extends Form{
    public $module;
    
    public function getFields() {
        return array (
            array (
                'label' => 'Action Name',
                'name' => 'actionName',
                'options' => array (
                    'ng-model' => 'active.name',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Template',
                'name' => 'Template',
                'options' => array (
                    'ng-model' => 'active.template',
                    'ng-hide' => 'edit == false',
                ),
                'listExpr' => 'ControllerGenerator::getTemplate();',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Parameters',
                'name' => 'params',
                'options' => array (
                    'ng-model' => 'active.param',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Form',
                'name' => 'Form',
                'options' => array (
                    'ng-model' => 'active.form',
                    'ng-hide' => 'edit == false',
                ),
                'listExpr' => 'FormBuilder::listForm($model->module, false);',
                'type' => 'DropDownList',
            ),
            '<hr>',
            array (
                'label' => 'Submit',
                'buttonSize' => '',
                'type' => 'SubmitButton',
            ),
        );
    }
    public function getForm() {
        return array (
            'formTitle' => 'Controller Editor',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'includeJS' => array (),
            'options' => array (
                'ng-submit' => '$event.preventDefault(); create();',
            ),
        );
    }
}
?>