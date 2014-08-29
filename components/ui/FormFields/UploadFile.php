<?php
class UploadFile extends FormField{
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
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
    
    /** @var string $name */
    public $name = '';
	
    /** @var string $value */
    public $value = '';
	
    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Upload File";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-eye-slash";
}
?>