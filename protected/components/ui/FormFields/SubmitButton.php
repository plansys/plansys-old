<?php

class SubmitButton extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Button Type',
                'name' => 'buttonType',
                'options' => array (
                    'ng-model' => 'active.buttonType',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'primary' => 'Primary',
                    'info' => 'Info',
                    'success' => 'Success',
                    'warning' => 'Warning',
                    'danger' => 'Danger',
                ),
                'listExpr' => 'array(
     \'primary\' => \'Primary\',
     \'info\' => \'Info\',
     \'success\' => \'Success\',
     \'warning\' => \'Warning\',
     \'danger\' => \'Danger\'
);',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Button Size',
                'name' => 'buttonSize',
                'options' => array (
                    'ng-model' => 'active.buttonSize',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'btn-xs' => 'Very Small',
                    'btn-sm' => 'Small',
                    '' => 'Default',
                    'btn-lg' => 'Large',
                ),
                'listExpr' => 'array(
    \'btn-xs\' => \'Very Small\',
    \'btn-sm\' => \'Small\',
    \'\' => \'Default\',
    \'btn-lg\' => \'Large\',
)',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Button Position',
                'name' => 'buttonPosition',
                'options' => array (
                    'ng-model' => 'active.buttonPosition',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ),
                'listExpr' => 'array(
   \'left\' => \'Left\',
   \'center\' => \'Center\',
   \'right\' => \'Right\',
);',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
        );
    }

    public $label = '';
    public $buttonType = 'primary';
    public $buttonSize = 'btn-lg';
    public $buttonPosition = 'center';
    public $options = array();
    public static $toolbarName = "Submit";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-hand-o-up";

    public function render() {
        $this->addClass('form-control');
        return $this->renderInternal('template_render.php');
    }

}
