<?php

class DevGenmodelGenAllModel extends Form {
    public $primaryKey = 'name';
    
    public function getForm() {
        return array (
            'title' => 'Generate All Model',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'newAllModel.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => '{{ btnTitle }}',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'genModel()',
                            'ng-disabled' => '!canGenerate',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'name' => 'db',
                        'label' => 'Database',
                        'filterType' => 'list',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'No',
                        'defaultValue' => 'db',
                        'showOther' => 'No',
                        'otherLabel' => '',
                        'queryOperator' => '',
                        'show' => false,
                        'listExpr' => 'array_keys(Setting::getDBListAll())',
                        'list' => array (
                            'db' => 'db',
                            '---' => '---',
                        ),
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"pull-right\" style=\"margin-top:-33px;border:1px solid red;color:red;padding:3px 10px;border-radius:4px;font-size:12px;\">
    <i class=\"fa fa-warning\"></i> Existing model will be overwritten
</div>',
            ),
            array (
                'name' => 'dataSource1',
                'fieldType' => 'php',
                'php' => 'ModelGenerator::listTablesGrid($params)',
                'postData' => 'No',
                'options' => array (
                    'primaryKey' => 'name',
                ),
                'type' => 'DataSource',
            ),
            array (
                'type' => 'GridView',
                'name' => 'gridView1',
                'label' => 'GridView',
                'datasource' => 'dataSource1',
                'gridOptions' => array (
                    'controlBar' => 'false',
                ),
                'columns' => array (
                    array (
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => array (
                            'width' => '50p',
                        ),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'mergeSameRowMethod' => 'Default',
                        'html' => '',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        '$listViewName' => 'columns',
                        'show' => true,
                        'cellMode' => 'default',
                    ),
                    array (
                        'name' => 'name',
                        'label' => 'Table Name',
                        'options' => array (),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'mergeSameRowMethod' => 'Default',
                        'html' => '',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        '$listViewName' => 'columns',
                        'show' => false,
                        'cellMode' => 'default',
                    ),
                    array (
                        'name' => 'model',
                        'label' => 'Model Name',
                        'options' => array (
                            'mode' => 'editable',
                        ),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'mergeSameRowMethod' => 'Default',
                        'html' => '<td ng-class=\"rowClass(row, \'model\', \'string\')\" >
    
    <div contenteditable=\"true\"  ng-model=\"row.model\"
         ng-keydown=\"editKey($event)\"></div>
</td>',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        '$listViewName' => 'columns',
                        'show' => false,
                        'cellMode' => 'custom',
                    ),
                    array (
                        'name' => 'chk',
                        'label' => '',
                        'options' => array (),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'mergeSameRowMethod' => 'Default',
                        'html' => '',
                        'columnType' => 'checkbox',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        '$listViewName' => 'columns',
                        'show' => false,
                        'cellMode' => 'default',
                        'checkedValue' => 'checked',
                    ),
                ),
            ),
        );
    }

}