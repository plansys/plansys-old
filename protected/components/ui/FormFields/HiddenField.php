<?php

class HiddenField extends FormField {

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
    
    public $name = '';
    public $value = '';
    public $isHidden = true;

    public static $toolbarName = "Hidden Field";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-eye-slash";

}
