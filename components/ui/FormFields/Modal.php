<?php
/**
 * Class Modal
 * @author rizky
 */
class Modal extends FormField {
    
    public $name = '';
    public $subForm = '';
    public $width = 400;
    public $height = 400;
    public $options = '';
    
    public static $toolbarName = "Modal Dialog";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-square-o";
    
    public function includeJS() {
        return array(
            'modal.js'
        );
    }
    
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
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Sub Form',
                'name' => 'subForm',
                'options' => array (
                    'ng-model' => 'active.subForm',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'FormBuilder::listForm()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}