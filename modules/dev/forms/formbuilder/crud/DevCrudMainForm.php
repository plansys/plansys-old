<?php

class DevCrudMainForm extends Form {

    public $name = '';
    public $path = '';
    public $model = '';
    
    ## Advanced Settings
    public $bulkCheckbox = 'Yes';
    public $softDelete = 'No';
    public $softDeleteColumn = '';
    public $softDeleteValue = '1';
    public $masterData = 'No';
    
    public function rules() {
        return [
            ['name','required']
        ];
    }
    
    public function getForm() {
        return array (
            'title' => 'Generate CRUD',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
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
                        'label' => 'Done',
                        'buttonType' => 'success',
                        'icon' => 'check',
                        'options' => array (
                            'ng-click' => 'done()',
                            'ng-if' => 'step == 5',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Back',
                        'icon' => 'chevron-left',
                        'options' => array (
                            'ng-click' => 'back()',
                            'ng-show' => 'step > 1 && step < 5',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Generate CRUD',
                        'buttonType' => 'success',
                        'icon' => 'magic',
                        'options' => array (
                            'ng-click' => 'generateNext()',
                            'ng-if' => 'step > 1 && step < 5',
                            'ng-class' => '{ disabled: step == 2 || step == 4 }',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Next Step',
                        'buttonType' => 'success',
                        'icon' => 'caret-right',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                            'ng-class' => '{ \'disabled\': !model.name }',
                            'ng-if' => 'step == 1',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-show=\'step == 1\'>',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Base Model',
                        'name' => 'model',
                        'options' => array (
                            'ng-change' => 'model.name = model.model; onNameChange();',
                        ),
                        'listExpr' => 'array_merge([\'\'=>\'Choose Model\'], ModelGenerator::listModels(true))',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Name',
                        'name' => 'name',
                        'options' => array (
                            'ng-change' => 'onNameChange()',
                            'ng-if' => '!!model.model',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Path Alias',
                        'value' => 'Path Alias: {{}}',
                        'js' => 'data.path',
                        'options' => array (
                            'ng-if' => '!!model.name',
                        ),
                        'fieldOptions' => array (
                            'name' => '',
                        ),
                        'type' => 'LabelField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<input type=\"hidden\" name=\"DevCrudMainForm[path]\" ng-value=\"data.path\">
',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'perColumnOptions' => array (
                    'style' => 'padding-right:0px;',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"$showAdvanced\\">',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Bulk Checkbox',
                        'name' => 'bulkCheckbox',
                        'listExpr' => '[\'Yes\',\'No\']',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Soft Delete',
                        'name' => 'softDelete',
                        'listExpr' => '[\'Yes\',\'No\']',
                        'type' => 'DropDownList',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"!!model.name\\">',
            ),
            array (
                'title' => 'Relations',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => ' </div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div ng-show=\'step > 1\'>',
            ),
            array (
                'type' => 'Text',
                'value' => '    <div style=\'margin-top:-2px\' class=\"alert alert-info\" ng-bind-html=\"msg\"></div>
    <table class=\"table table-bordered table-condensed\">
        <tr>
            <th style=\'width:20px;\'></th>
            <th style=\'width:10%;\'>Type</th>
            <th style=\'width:45%;\'>Name</th>
            <th style=\'width:30%;\'>Status</th>
            <th ng-if=\"step == 3\" style=\'width:15%;text-align:center;\'>
                <label style=\'margin:0px;\' ng-if=\"exists.length > 0\">
                    <input style=\'margin:0px;\' ng-click=\'checkAll($event)\' type=\"checkbox\">&nbsp;Check all
                </label>
            </th>
        </tr>
        <tr ng-repeat=\'f in data.files\'>
            <td>
                <i class=\"fa fa-check-square-o\"  ng-if=\"f.status == \'ready\'\" style=\'color:green\'></i>
                <i class=\"fa fa-minus-square\"  ng-if=\"f.status == \'exist\'\"></i>
                <i class=\"fa fa-refresh fa-spin\"  ng-if=\"f.status == \'processing\'\"></i>
                <i class=\"fa fa-minus-square\"  ng-if=\"f.status == \'skipped\'\" style=\'color:orange\'></i>
                <i class=\"fa fa-check-square\"  ng-if=\"f.status == \'ok\'\" style=\'color:green\'></i>
            </td>
            <td>{{f.type}}</td>
            <td>{{f.name}}</td>
            <td>
                <div class=\"label label-default\" style=\"text-transform:uppercase;\">{{f.status == \'exist\' ? \'FILE EXISTS\' : f.status}}</div>
            </td>
            <td style=\'text-align:center;\' ng-if=\"step == 3\">
                <label style=\'margin:0px;\' ng-if=\'f.status == \"exist\"\' >
                    <input style=\'margin:0px;\' type=\"checkbox\" ng-model=\'f.overwrite\'>&nbsp;Overwrite
                </label>
            </td>
        </tr>
    </table>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}