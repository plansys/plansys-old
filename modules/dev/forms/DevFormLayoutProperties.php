<?php

class DevFormLayoutProperties extends Form {

    public $module;
    
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
            '<hr/>',
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
            '<div class="clearfix"></div><hr/>',
            array (
                'label' => 'Menu Tree',
                'options' => array (
                    'ng-model' => 'layout.file',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-show' => 'layout.type == \'menu\'',
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
                    'ng-show' => 'layout.type == \'menu\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Sub Form',
                'options' => array (
                    'ng-model' => 'layout.class',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-show' => 'layout.type == \'form\'',
                ),
                'listExpr' => 'FormBuilder::listForm($model->module)',
                'type' => 'DropDownList',
            ),
            '<hr ng-show="layout.type == \'menu\'"/>',
            array (
                'label' => '<div class=\\"label label-default pull-right\\" style=\\"margin-right:15px;\\">(ng-click attribute)</div>Menu On-Click:',
                'labelWidth' => '3',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'fieldHeight' => '5',
                'options' => array (
                    'ng-model' => 'layout.onclick',
                    'ng-change' => 'changeLayoutProperties()',
                    'ng-delay' => '500',
                    'ng-show' => 'layout.type == \'menu\'',
                ),
                'type' => 'TextArea',
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
