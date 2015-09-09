<?php

class DevFormLayoutProperties extends Form {

    public $module;
    public $menuOptions = array(
        'ng-click' => 'toggle(this);select(this);'
    );
    
    public function getFields() {
        return array (
            array (
                'label' => 'Layout Type',
                'name' => 'layoutType',
                'options' => array (
                    'ng-model' => 'layout.type',
                    'ng-change' => 'changeLayoutSectionType()',
                ),
                'list' => array (
                    'mainform' => 'Main Form',
                    'menu' => 'Menu Tree',
                    '' => 'None',
                ),
                'fieldWidth' => '5',
                'type' => 'DropDownList',
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
                    'ng-change' => 'changeMenuTreeFile()',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'listExpr' => 'MenuTree::listDropdown($model->module)',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Title',
                'options' => array (
                    'ng-model' => 'layout.title',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Icon',
                'listExpr' => 'Helper::iconList()',
                'renderEmpty' => 'Yes',
                'iconTemplate' => '<i class=\\"fa fa-fw fa-lg {icon}\\"></i>',
                'fieldWidth' => '180',
                'options' => array (
                    'ng-model' => 'layout.icon',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'type' => 'IconPicker',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-show=\\"layout.type == \\\'menu\\\'\\"/>',
            ),
            array (
                'label' => 'Inline JS File',
                'options' => array (
                    'ng-model' => 'layout.inlineJS',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \\\'menu\\\'',
                ),
                'type' => 'TextField',
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