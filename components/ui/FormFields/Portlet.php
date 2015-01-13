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

    public function getDashboardMode() {
        return (Yii::app()->controller->module->id == "dev" &&
                Yii::app()->controller->id == "forms" &&
                Yii::app()->controller->action->id == "dashboard" ? "edit" : "view");
    }

    public function includeJS() {
        if ($this->dashboardMode == "edit") {
            return ['js/interact-1.2.1.min.js', 'js/portlet-editor.js'];
        } else {
            return ['js/interact-1.2.1.min.js', 'js/portlet.js'];
        }
    }

    /** @var string $toolbarName */
    public static $toolbarName = "Portlet";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-square-o";

    public function render() {
        if ($this->dashboardMode == "edit") {
            return $this->renderInternal('template_dashboard.php');
        } else {
            return $this->renderInternal('template_render.php');
        }
    }

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
                'value' => '<hr>',
                'type' => 'Text',
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
                        'label' => 'X',
                        'name' => 'top',
                        'options' => array (
                            'ng-model' => 'active.left',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'W',
                        'name' => 'width',
                        'options' => array (
                            'ng-model' => 'active.width',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Y',
                        'name' => 'left',
                        'options' => array (
                            'ng-model' => 'active.top',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'H',
                        'name' => 'height',
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
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}