<?php

/**
 * Class Colorpicker
 * @author rizky
 */
class Colorpicker extends FormField {

    /**
     * @return array me-return array property TextField.
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
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
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
                'label' => 'Field Type',
                'name' => 'fieldType',
                'options' => array (
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'text' => 'Text Field',
                    'password' => 'Password Field',
                ),
                'showOther' => 'Yes',
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \\\'Vertical\\\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column3' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column4' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '<hr/>',
                'type' => 'Text',
            ),
            array (
                'column1' => array (
                    array (
                        'name' => 'prefix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Prefix',
                        'options' => array (
                            'ng-model' => 'active.prefix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'name' => 'postfix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Postfix',
                        'options' => array (
                            'ng-model' => 'active.postfix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name = '';

    /** @var string $fieldType */
    public $fieldType = 'text';

    /** @var string $value */
    public $value = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var array $options */
    public $options = array();

    /** @var array $labelOptions */
    public $labelOptions = array();

    /** @var array $fieldOptions */
    public $fieldOptions = array();

    /** @var string $toolbarName */
    public static $toolbarName = "Colorpicker";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-eyedropper";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('colorpicker.js');
    }

    /**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        return $this->renderInternal('template_render.php');
    }

}