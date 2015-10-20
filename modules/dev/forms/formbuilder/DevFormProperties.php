<?php

class DevFormProperties extends Form {

    public $title;
    public $layoutName;
    public $options = array();
    public $inlineJS = "";
    public $includeJS = array();
    public $includeCSS = array();

    public function getForm() {
        return array (
            'title' => 'FormProperties',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'includeJS' => array (),
        );
    }

    public function getFields() {
        return array (
            array (
                'label' => 'Form Title',
                'name' => 'title',
                'options' => array (
                    'ng-model' => '$parent.form.title',
                    'ng-change' => 'saveForm();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Form Layout',
                'name' => 'layoutName',
                'listExpr' => 'Layout::listLayout()',
                'iconTemplate' => '<img src=\\"{plansys_url}/static/img/columns/{icon}.png\\" />',
                'fieldWidth' => '150',
                'options' => array (
                    'ng-model' => '$parent.form.layout.name',
                    'ng-change' => 'changeLayoutType(form.layout.name)',
                ),
                'type' => 'IconPicker',
            ),
            array (
                'label' => 'Base Class',
                'name' => 'extendsFrom',
                'options' => array (
                    'ng-model' => '$parent.form.extendsFrom',
                    'ng-change' => 'saveForm();',
                    'ng-delay' => '500',
                ),
                'listExpr' => 'ModelGenerator::listModels();',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom Class',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<a ng-show=\"$parent.form.extendsFrom != \'Form\'\" ng-href=\"{{Yii.app.createUrl(\'/dev/genModel/index\', {active:$parent.form.extendsFrom})}}\" 
    style=\"margin-bottom:5px;\"
    target=\"_blank\"
    class=\"pull-right btn btn-default btn-xs\">
    <i class=\"fa fa-sign-in\"></i>
    Edit Base Class
</a>
<div class=\"clearfix\"></div>',
            ),
            array (
                'label' => 'Inline JS File',
                'name' => 'inlineJS',
                'options' => array (
                    'ng-model' => '$parent.form.inlineJS',
                    'ng-change' => 'saveForm();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<a ng-href=\"{{Yii.app.createUrl(\'/dev/forms/code\', {
    c: classPath,
    s: $parent.form.inlineJS
})}}\" 
    style=\"margin-bottom:5px;\"
    class=\"pull-right btn btn-default btn-xs\">
    <i class=\"fa fa-sign-in\"></i>
    Edit Inline JS
</a>
<div class=\"clearfix\"></div>',
            ),
            array (
                'name' => 'includeJS',
                'options' => array (
                    'ng-model' => '$parent.form.includeJS',
                    'ng-change' => 'saveForm()',
                    'class' => 'flat',
                    'unique' => 'true',
                ),
                'singleView' => 'DropDownList',
                'singleViewOption' => array (
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array (
                        'ng-delay' => 500,
                    ),
                    'list' => array (),
                ),
                'type' => 'ListView',
            ),
            array (
                'label' => 'Form Options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => '$parent.form.options',
                    'ng-change' => 'saveForm()',
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }
}