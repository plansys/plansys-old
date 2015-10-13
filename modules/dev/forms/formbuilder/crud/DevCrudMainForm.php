<?php

class DevCrudMainForm extends Form {

    public $name = '';
    public $path = '';
    public $model = '';
    
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
                        'label' => 'Generate CRUD',
                        'buttonType' => 'success',
                        'icon' => 'magic',
                        'options' => array (
                            'ng-click' => 'checkFile()',
                            'ng-if' => 'step == 2',
                            'ng-class' => '{ disabled: step == 2 }',
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
                'value' => '        <div ng-if=\\"!!model.name\\">',
            ),
            array (
                'title' => 'Relations',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '        </div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div ng-show=\'step > 1\'>',
            ),
            array (
                'type' => 'Text',
                'value' => '    <div style=\'margin-top:-2px\' class=\"alert alert-info\">
        {{ msg }}
    </div>
    
    <table class=\"table table-bordered table-condensed\">
        <tr>
            <th style=\'width:20px;\'></th>
            <th style=\'width:10%;\'>Type</th>
            <th style=\'width:45%;\'>Name</th>
            <th style=\'width:30%;\'>Status</th>
            <th style=\'width:15%;text-align:center;\'>
Overwrite&nbsp;<input type=\"checkbox\">
            </th>
        </tr>
        <tr ng-repeat=\'f in data.files\'>
            <td>
                <i class=\"fa fa-check\"  ng-if=\"f.status == \'ready\'\" style=\'color:green\'></i>
                <i class=\"fa fa-times\"  ng-if=\"f.status == \'exists\'\" style=\'color:red\'></i>
            </td>
            <td>{{f.type}}</td>
            <td>{{f.name}}</td>
            <td>{{f.status}}</td>
            <td style=\'text-align:center;\'><input ng-if=\'f.status == \"exists\" type=\"checkbox\" ng-model=\'overwrite\'></td>
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