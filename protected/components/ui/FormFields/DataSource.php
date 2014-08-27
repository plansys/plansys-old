<?php

/**
 * Class DataSource
 * @author rizky
 */
class DataSource extends FormField {

    /**
     * @return array me-return array property DataSource.
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
    
    /** @var string $fieldType */
    public $fieldType = 'sql';
    
    /** @var string $sql */
    public $sql = '';
    
    /** @var string $php */
    public $php = '';
    
    /** @var string $params */
    public $params = '';

    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Data Source";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-book";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('data-source.js');
    }

}
