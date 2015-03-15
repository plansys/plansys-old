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
                'type' => 'Text',
                'value' => '
<div class=\"col-sm-2\"></div>
<div class=\"col-sm-8\" style=\"padding-top:40px;\">
    <div class=\"panel panel-default\">
      <div class=\"panel-heading\">
          <i class=\"fa fa-lg fa-trello\" style=\"margin:0px 5px 0px -5px;\"></i>
          Generic CRUD Form
      </div> 
      <div class=\"panel-body\" style=\"padding:0px;\">',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Module',
                        'name' => 'module',
                        'listExpr' => 'FormBuilder::listModule()',
                        'searchable' => 'Yes',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
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
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '      </div>
    </div>
</div>
<div class=\"col-sm-2\"></div>
',
            ),
        );
    }

}