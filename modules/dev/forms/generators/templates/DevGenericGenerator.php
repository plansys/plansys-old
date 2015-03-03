<?php

class DevGenericGenerator extends Form {
    
    public $module = "";
    public $tableName = "";
    public $model = "";
    
    public function getForm() {
        return array (
            'title' => 'Generic Generator',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '
<div class=\"col-sm-2\"></div>
<div class=\"col-sm-8\" style=\"padding-top:40px;\">
    <div class=\"panel panel-default\">
      <div class=\"panel-heading\">
          <i class=\"fa fa-lg fa-trello\" style=\"margin:0px 5px 0px -5px;\"></i>
          Generic CRUD Form
      </div> 
      <div class=\"panel-body\" style=\"padding:0px;\">',
                'type' => 'Text',
            ),
            array (
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Module',
                        'name' => 'module',
                        'listExpr' => 'FormBuilder::listModule()',
                        'searchable' => 'Yes',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Table Name',
                        'name' => 'tableName',
                        'listExpr' => 'ActiveRecord::listTables()',
                        'searchable' => 'Yes',
                        'type' => 'DropDownList',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Model Name',
                        'name' => 'model',
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '      </div>
    </div>
</div>
<div class=\"col-sm-2\"></div>
',
                'type' => 'Text',
            ),
        );
    }

}