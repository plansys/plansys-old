<?php

/**
 * Class Portlet
 * @author rizky
 */
class Portlet extends FormField {

    public $name;
    public $title;
    public $top = "";
    public $left = "";
    public $width = 400;
    public $height = 300;
    public $items = ['<column-placeholder></column-placeholder>'];
    public $renderItems;
    public $parseField = [
        'items' => 'renderItems'
    ];
    public $options = [];
    
    public $showBorder = 'Yes';
    public $zoomable = 'Yes';
    
    public function includeJS() {
        return ['js/interact-1.2.1.min.js', 'js/portlet.js'];
    }
    
    /** @var string $toolbarName */
    public static $toolbarName = "Portlet";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-square-o";

    /**
     * @return array me-return array property ActionBar.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Portlet Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Title',
                'name' => 'title',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'auto-grow' => 'true',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Show Border',
                'name' => 'showBorder',
                'options' => array (
                    'ng-model' => 'active.showBorder',
                    'ng-change' => 'save()',
                ),
                'listExpr' => '[\\\'Yes\\\',\\\'No\\\']',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Zoomable',
                'name' => 'zoomable',
                'options' => array (
                    'ng-model' => 'active.zoomable',
                    'ng-change' => 'save()',
                ),
                'listExpr' => '[\\\'Yes\\\',\\\'No\\\']',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'value' => '<hr>',
                'type' => 'Text',
            ),
            array (
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Left',
                        'name' => 'left',
                        'labelWidth' => '3',
                        'fieldWidth' => '9',
                        'postfix' => 'px',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Width',
                        'name' => 'width',
                        'fieldType' => 'number',
                        'labelWidth' => '3',
                        'fieldWidth' => '9',
                        'postfix' => 'px',
                        'options' => array (
                            'ng-model' => 'active.width',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Top',
                        'name' => 'top',
                        'labelWidth' => '3',
                        'fieldWidth' => '9',
                        'postfix' => 'px',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Height',
                        'name' => 'height',
                        'fieldType' => 'number',
                        'labelWidth' => '3',
                        'fieldWidth' => '9',
                        'postfix' => 'px',
                        'options' => array (
                            'ng-model' => 'active.height',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '<hr>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}