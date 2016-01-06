<?php

class DataSourceAggregateCol extends Form {

    public $col   = '';
    public $colType = ''; // sum, avg, min, max, count
    public $customType = '';

    public function getForm() {
        return array(
            'title' => 'Source Group',
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
                'value' => '</div>
<div style=\"float:right;width:30%;\">
',
            ),
            array (
                'name' => 'colType',
                'options' => array (
                    'style' => 'margin-bottom:0px',
                    'ng-model' => 'item.colType',
                    'ng-change' => 'udpateListItem()',
                ),
                'menuPos' => 'pull-right',
                'defaultType' => 'first',
                'list' => array (
                    'sum' => 'sum',
                    'avg' => 'avg',
                    'count' => 'count',
                    'max' => 'max',
                    'min' => 'min',
                    '---' => '---',
                    'custom' => 'custom',
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
                'label' => 'PHP Expression',
                'fieldname' => 'customType',
                'options' => array (
                    'ng-if' => 'item.colType == \'custom\'',
                    'style' => 'margin:5px 0px 2px 0px',
                    'ng-model' => 'item.customType',
                ),
                'desc' => 'Example:<pre style=\"margin:5px 0px;font-size:11px;color:#555;background:white\">\"TOTAL:\" . $sum(\'column\')</pre> 
Available Function: $sum(), $avg(), $count(), $max(), $min(), $first(), $last(), $text()',
                'type' => 'ExpressionField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"height:5px\\"></div>',
            ),
        );
    }

}