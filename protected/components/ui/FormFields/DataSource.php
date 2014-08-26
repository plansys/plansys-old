<?php

/**
 * Class HiddenField
 * @author rizky
 */
class DataSource extends FormField {

    /**
     * @return array Fungsi ini akan me-return array property HiddenField.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Data Source Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Source Type',
                'name' => 'fieldType',
                'options' => array (
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save()',
                ),
                'list' => array (
                    'sql' => 'SQL',
                    'php' => 'PHP Function',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'SQL',
                'fieldname' => 'sql',
                'language' => 'sql',
                'options' => array (
                    'ng-show' => 'active.fieldType == \'sql\'',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'PHP Function',
                'fieldname' => 'php',
                'options' => array (
                    'ng-show' => 'active.fieldType == \'php\'',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Parameters',
                'fieldname' => 'params',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /** @var string $name */
    public $name = '';
    public $fieldType = 'sql';
    public $sql = '';
    public $php = '';
    public $params = '';

    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Data Source";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-book";

    public function includeJS() {
        return array('data-source.js');
    }

}
