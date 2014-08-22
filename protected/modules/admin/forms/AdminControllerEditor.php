<?php
class AdminControllerEditor extends Form{
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
                ),
                'list' => array (
                    'default' => 'Default Action',
                    'index' => 'actionIndex',
                    'create' => 'actionCreate',
                    'update' => 'actionUpdate',
                    'delete' => 'actionDelete',
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
                'fieldOptions' => array (
                    'ng-list' => '',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Form',
                'name' => 'Form',
                'options' => array (
                    'ng-model' => 'active.form',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Submit',
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
        );
    }
}
?>