<?php

class DevFormNewForm extends Form {

    public $formName = '';
    public $modelName = '';
    public $customClassName = '';
    public $baseClass = 'Form';

    public function getForm() {
        return array(
            'title' => 'New Form',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'newForm.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => 'Create New Form',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'label' => 'Form Name:',
                'name' => 'formName',
                'layout' => 'Vertical',
                'prefix' => '{{ params.prefix }}',
                'options' => array (
                    'style' => 'margin-top:15px',
                    'ng-change' => 'onFormNameChange()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Base Class',
                'name' => 'baseClass',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'Form' => 'Form',
                    '--model--' => 'ActiveRecord Model',
                    '--custom--' => 'Custom Class',
                ),
                'layout' => 'Vertical',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'ActiveRecord Model',
                'name' => 'modelName',
                'options' => array (
                    'ng-if' => 'model.baseClass == \'--model--\'',
                ),
                'defaultType' => 'first',
                'listExpr' => 'ModelGenerator::listModels(true)',
                'layout' => 'Vertical',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Class Name',
                'name' => 'customClassName',
                'layout' => 'Vertical',
                'options' => array (
                    'ng-show' => 'model.baseClass == \'--custom--\'',
                ),
                'type' => 'TextField',
            ),
        );
    }

}