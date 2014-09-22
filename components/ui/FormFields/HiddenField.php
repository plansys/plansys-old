<?php
/**
 * Class HiddenField
 * @author rizky
 */
class HiddenField extends FormField {
    /**
     * @return array me-return array property HiddenField.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'listExpr' => 'FormsController::$modelFieldList',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
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
    
    /** @var string $name */
    public $name = '';
	
    /** @var string $value */
    public $value = '';
	
    public $options = array();
    
    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Hidden Field";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-eye-slash";

}