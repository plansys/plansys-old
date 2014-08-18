<?php

class HiddenField extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property HiddenField.
	 */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-form-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'list' => array (
                    'name' => 'name',
                    'value' => 'value',
                    'isHidden' => 'isHidden',
                    'parseField' => 'parseField',
                    'renderID' => 'renderID',
                ),
                'listExpr' => 'FormsController::$modelFieldList',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
        );
    }
    
	/** @var string variable untuk menampung name */
    public $name = '';
	
	/** @var string variable untuk menampung value */
    public $value = '';
	
	/** @var boolean variable untuk menampung kondisi field dengan default True atau hidden */
    public $isHidden = true;
	
	/** @var string variable untuk menampung toolbarName */
    public static $toolbarName = "Hidden Field";
	
	/** @var string variable untuk menampung category */
    public static $category = "User Interface";
	
	/** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon = "fa fa-eye-slash";
	

}
