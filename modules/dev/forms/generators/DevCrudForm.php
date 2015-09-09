<?php

class DevCrudForm extends Form {

    public $module;
    public $model;
    public $tableName;


    public function getForm() {
        return array(
            'title' => 'CRUD Generator',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'crud.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Generate CRUD',
                        'buttonType' => 'success',
                        'icon' => 'check',
                        'options' => array (
                            'ng-click' => 'generate();',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Module',
                        'name' => 'module',
                        'layout' => 'Vertical',
                        'fieldWidth' => '12',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Table Name',
                        'name' => 'tableName',
                        'options' => array (
                            'ng-change' => 'model.model = snakeToCamel(model.tableName)',
                        ),
                        'listExpr' => 'DevCrudForm::getTables()',
                        'layout' => 'Vertical',
                        'fieldWidth' => '12',
                        'searchable' => 'Yes',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Model',
                        'name' => 'model',
                        'layout' => 'Vertical',
                        'fieldWidth' => '12',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<br/>
Status: <b>{{status}}</b>

<div style=\"margin-top:10px\" class=\"progress\">
  <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"{{progress}}\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: {{progress}}%;\">
    <span class=\"sr-only\">{{progress}}% Complete</span>
  </div>
</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<pre ng-if=\\"!error\\" style=\\"margin-top:10px;\\"ng-bind-html=\\"result\\"></pre>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Debug Message',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"margin-top:10px;\\"ng-bind-html=\\"result\\"></div>',
            ),
        );
    }

}