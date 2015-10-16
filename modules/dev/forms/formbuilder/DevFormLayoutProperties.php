<?php

class DevFormLayoutProperties extends Form {

    public $module;
    public $menuOptions = array(
        'ng-click' => 'toggle(this);select(this);'
    );
    
    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div style=\\"padding:0px 5px;\\">',
            ),
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
                'label' => 'Title',
                'options' => array (
                    'ng-model' => 'layout.title',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \'menu\'',
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
                    'ng-show' => 'layout.type == \'menu\'',
                ),
                'type' => 'IconPicker',
            ),
            array (
                'label' => 'Menu Tree',
                'options' => array (
                    'ng-model' => 'layout.file',
                    'ng-change' => 'changeMenuTreeFile()',
                    'ng-show' => 'layout.type == \'menu\'',
                ),
                'listExpr' => 'MenuTree::listDropdown($model->module)',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Edit Menu',
                'icon' => 'sign-in',
                'position' => 'right',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 5px;',
                    'ng-click' => 'popupWindow1.open()',
                    'ng-if' => 'layout.type == \'menu\'',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\\"clearfix\\"></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-show=\\"layout.type == \'menu\'\\"/>',
            ),
            array (
                'label' => 'Inline JS File',
                'options' => array (
                    'ng-model' => 'layout.inlineJS',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \'menu\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<a ng-href=\"{{Yii.app.createUrl(\'/dev/forms/code\', {
    c: classPath,
    s: layout.inlineJS,
    m: 1
})}}\" 
    style=\"margin-bottom:5px;\"
    class=\"pull-right btn btn-default btn-xs\">
    <i class=\"fa fa-sign-in\"></i>
    Edit Inline JS
</a>
<div class=\"clearfix\"></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr ng-show=\\"layout.type == \'menu\'\\"/>',
            ),
            array (
                'label' => 'Menu Options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'layout.menuOptions',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-show' => 'layout.type == \'menu\'',
                ),
                'type' => 'KeyValueGrid',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'popupWindow1',
                'options' => array (
                    'width' => '800',
                    'height' => '500',
                ),
                'mode' => 'url',
                'url' => '/dev/genMenu/update&class={{layout.file}}',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
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