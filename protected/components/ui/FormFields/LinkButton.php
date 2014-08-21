<?php

class LinkButton extends FormField {

	/** @var string $label */
    public $label = '';
	
	/** @var string $url */
    public $url = '#';
	
	/** @var array $urlparams */
    public $urlparams = array();
	
	/** @var string $group */
    public $group = '';
	
	/** @var string $groupType */
    public $groupType = 'ButtonGroup';
	
	/** @var string $buttonType */
    public $buttonType = 'primary';
	
	/** @var string $icon */
    public $icon = '';
	
	/** @var string $buttonSize */
    public $buttonSize = '';
	
	/** @var array $options */
    public $options = array();
	
	/** @var boolean $displayInline */
    public $displayInline = true;
	
	/** @var string $toolbarName */
    public static $toolbarName = "Link Button";
	
	/** @var string $category */
    public static $category = "User Interface";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-unlink";
	
	/**
	 * @return array Fungsi ini akan me-return array javascript yang di-include. Defaultnya akan meng-include.
	*/
    public function includeJS() {
        return array('link-button.js');
    }

	/**
	 * @param string $url sebuah rute url.
	 * @return string Fungsi ini akan me-return constructed URL.  
	*/
    public function createUrl($url) {
        if ($url == "#") {
            return "#";
        } else {
            return Yii::app()->controller->createUrl($url);
        }
    }

	/**
	 * @return array Fungsi ini akan me-return array property LinkButton.
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
                'label' => 'Group',
                'name' => 'group',
                'options' => array (
                    'ng-model' => 'active.group',
                    'ng-change' => 'save();console.log($event);',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Group Type',
                'name' => 'groupType',
                'options' => array (
                    'ng-show' => 'active.group.trim() != \'\'',
                    'ng-model' => 'active.groupType',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'ButtonGroup\\\', \\\'DropDown\\\')',
                'type' => 'DropDownList',
            ),
            '<Hr/>',
            array (
                'label' => 'Url',
                'name' => 'url',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Url Parameters',
                'fieldname' => 'urlparams',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            '<Hr/>',
            array (
                'label' => 'Icon',
                'name' => 'icon',
                'fieldWidth' => '7',
                'prefix' => 'fa-',
                'options' => array (
                    'ng-model' => 'active.icon',
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
                'label' => 'Options',
                'fieldname' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}
