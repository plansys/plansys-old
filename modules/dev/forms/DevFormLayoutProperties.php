<?php

class DevFormLayoutProperties extends Form {

    public $module;
    public $menuOptions = array(
        'ng-click' => 'toggle(this);select(this);'
    );
    
    public function getFields() {
        return array (
            array (
                'name' => 'layoutType',
                'list' => array (
                    'mainform' => 'Main Form',
                    'menu' => 'Menu Tree',
                    'form' => 'Sub Form',
                    '' => 'None',
                ),
                'layout' => 'Vertical',
                'itemLayout' => 'ButtonGroup',
                'labelWidth' => '3',
                'options' => array (
                    'ng-model' => 'layout.type',
                    'ng-change' => 'changeLayoutSectionType()',
                    'style' => 'text-align:center;margin-top:-6px;',
                ),
                'type' => 'RadioButtonList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Size',
                'fieldWidth' => 5,
                'postfix' => 'px',
                'options' => array (
                    'ng-model' => 'layout.size',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => '...',
                    'style' => 'text-align:center;',
                ),
                'type' => 'TextField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\\"clearfix\\"></div><hr/>',
            ),
            array (
                'label' => 'Menu Tree',
                'options' => array (
                    'ng-model' => 'layout.file',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'listExpr' => 'MenuTree::listHtml($model->module)',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Header',
                'options' => array (
                    'ng-model' => 'layout.title',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Sub Form',
                'options' => array (
                    'ng-model' => 'layout.class',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-show' => 'layout.type == \\\'form\\\'',
                ),
                'listExpr' => 'FormBuilder::listForm($model->module)',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-show=\\"layout.type == \\\'menu\\\'\\"/>',
            ),
            array (
                'label' => 'Menu Options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'layout.menuOptions',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public function getForm() {
        return array(
            'title' => 'FormLayoutProperties',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

}