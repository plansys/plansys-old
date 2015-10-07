<?php

class DataSourceAggregateGroup extends Form {

    public $col  = '';
    public $mode = 'default'; //default , sql
    public $sql  = ''; 

    public function getForm() {
        return array(
            'title' => 'Columns Aggregate',
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

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div>
<div style=\"float:left;width:70%;\">
',
            ),
            array (
                'name' => 'col',
                'options' => array (
                    'style' => 'margin-bottom:0px',
                    'ps-list' => 'dsGroupCols',
                    'ng-init' => 'getDSGroupCols();',
                    'ng-model' => 'item.col',
                    'ng-change' => 'udpateListItem()',
                    'ng-if' => '$index > 0',
                ),
                'list' => array (),
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom Column',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\"$index == 0\" style=\"
    text-align: center;
    padding-top: 7px;
    font-size: 12px;
    font-weight: bold;\">
    &mdash; ALL COLUMNS  &mdash;
</div>
<div ng-init=\"item.col = ($index == 0 ? \'-all-\' : item.col == \'-all-\' ? \'\': item.col);\"></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div style=\"float:right;width:30%;\">
',
            ),
            array (
                'name' => 'mode',
                'options' => array (
                    'style' => 'margin-bottom:0px',
                    'ng-model' => 'item.mode',
                    'ng-change' => 'udpateListItem()',
                ),
                'menuPos' => 'pull-right',
                'list' => array (
                    'default' => 'default',
                    'sql' => 'sql',
                ),
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'otherLabel' => 'Custom Function',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
</div>
<div class=\"clearfix\"></div>',
            ),
            array (
                'label' => 'Aggregation SQL',
                'fieldname' => 'sql',
                'language' => 'sql',
                'options' => array (
                    'ng-if' => 'item.mode == \'sql\'',
                    'style' => 'margin:5px 0px 2px 0px',
                    'ng-model' => 'item.sql',
                ),
                'desc' => 'Replace default aggregation using this sql:
<hr/>
Use {sql} to get current sql, for example: 
<pre style=\"margin:0px;font-size:11px;color:#555;background:white\">SELECT * FROM ({sql}) GROUP BY id</pre>',
                'type' => 'ExpressionField',
            ),
            array (
                'type' => 'Text',
                'value' => '
<div style=\"height:5px\"></div>',
            ),
        );
    }

}