<?php

class SubmitButton extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property SubmitButton.
	 */
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

	/** @var string $label */
    public $label = '';
	
	/** @var string $buttonType */
    public $buttonType = 'primary';
	
	/** @var string $buttonSize */
    public $buttonSize = 'btn-lg';
	
	/** @var string $buttonPosition */
    public $buttonPosition = 'center';
	
	/** @var array $options */
    public $options = array();
	
	/** @var string $toolbarName */
    public static $toolbarName = "Submit";
	
	/** @var string $category */
    public static $category = "User Interface";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-hand-o-up";
	
	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
	 */	
    public function render() {
        $this->addClass('form-control');
        return $this->renderInternal('template_render.php');
    }

}
