<?php
/**
 * Class SubmitButton
 * @author rizky
 */
class SubmitButton extends FormField {
    /**
     * @return array me-return array property SubmitButton.
     */
    public function getFieldProperties() {
        return  [
             [
                'label' => 'Label',
                'name' => 'label',
                'options' =>  [
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Button Type',
                'name' => 'buttonType',
                'options' =>  [
                    'ng-model' => 'active.buttonType',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(
     \'primary\' => \'Primary\',
     \'info\' => \'Info\',
     \'success\' => \'Success\',
     \'warning\' => \'Warning\',
     \'danger\' => \'Danger\'
);',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Button Size',
                'name' => 'buttonSize',
                'options' =>  [
                    'ng-model' => 'active.buttonSize',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(
    \'btn-xs\' => \'Very Small\',
    \'btn-sm\' => \'Small\',
    \'\' => \'Default\',
    \'btn-lg\' => \'Large\',
)',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Button Position',
                'name' => 'buttonPosition',
                'options' =>  [
                    'ng-model' => 'active.buttonPosition',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(
   \'left\' => \'Left\',
   \'center\' => \'Center\',
   \'right\' => \'Right\',
);',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ],
        ];
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
    public $options = [];
	
    /** @var string $toolbarName */
    public static $toolbarName = "Submit";
	
    /** @var string $category */
    public static $category = "User Interface";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-hand-o-up";
	
    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut SubmitButton dari hasil render
     */
    public function render() {
        $this->addClass('form-control');
        return $this->renderInternal('template_render.php');
    }

}
