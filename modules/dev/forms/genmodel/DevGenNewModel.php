<?php

class DevGenNewModel extends Form {

    public $module;
    public $modelName;
    public $tableName;
    public $softDelete = 'No';
    public $softDeleteColumn = '';
    public $softDeleteValue = '1';

    public function getForm() {
        return array(
            'title' => 'Generate New Model',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'newModel.js',
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
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"height:15px\\"></div>',
            ),
            array (
                'name' => 'module',
                'type' => 'HiddenField',
            ),
            array (
                'label' => 'Table Name',
                'name' => 'tableName',
                'options' => array (
                    'ng-change' => 'updateTable(); getListField();',
                ),
                'listExpr' => 'ModelGenerator::listTables()',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Model Name',
                'name' => 'modelName',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Soft Delete',
                'name' => 'softDelete',
                'options' => array (
                    'ng-change' => 'getListField()',
                    'ng-if' => '!!model.modelName',
                ),
                'listExpr' => '[\'No\', \'Yes\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Soft Delete Column',
                'name' => 'softDeleteColumn',
                'options' => array (
                    'ps-list' => 'fieldList',
                    'ng-if' => 'model.softDelete == \'Yes\'',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Soft Delete Value',
                'name' => 'softDeleteValue',
                'options' => array (
                    'ng-if' => 'model.softDelete == \'Yes\'',
                ),
                'type' => 'TextField',
            ),
        );
    }

}