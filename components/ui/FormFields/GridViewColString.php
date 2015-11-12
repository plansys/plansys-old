<?php

class GridViewColString extends Form {

    public $html = '';

    public function getForm() {
        return array (
            'title' => '',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'label' => 'Header',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'item.label',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                    'ng-if' => 'item.columnType == \'string\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Merge Same Row',
                'name' => 'mergeSameRow',
                'options' => array (
                    'ng-model' => 'item.mergeSameRow',
                    'ng-change' => 'updateListView()',
                ),
                'menuPos' => 'pull-right',
                'defaultType' => 'first',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'labelWidth' => '8',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Merge Row With',
                'name' => 'mergeSameRowWith',
                'options' => array (
                    'ng-if' => 'item.mergeSameRow == \'Yes\'',
                    'ng-model' => 'item.mergeSameRowWith',
                    'ng-change' => 'updateListView()',
                    'ng-init' => 'getDSGroupCols()',
                    'ps-list' => 'dsGroupCols',
                ),
                'menuPos' => 'pull-right',
                'defaultType' => 'first',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'Custom Column',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Cell Mode',
                'name' => 'cellMode',
                'options' => array (
                    'ng-model' => 'item.cellMode',
                    'ng-change' => 'getCellTemplate(item, $index);updateListView();',
                ),
                'defaultType' => 'first',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    'default' => 'Default',
                    'custom' => 'Custom',
                ),
                'labelWidth' => '7',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'AceEditor',
                'name' => 'html',
                'label' => 'HTML Content:',
                'options' => array (
                    'ng-model' => 'item.html',
                    'ng-change' => 'updateListView();',
                    'ng-delay' => '500',
                    'style' => 'height:100px;width:100%;position:relative !important;font-size:12px;border:1px solid #ddd;margin-bottom:5px',
                ),
                'containerOptions' => array (
                    'ng-if' => 'item.cellMode == \'custom\'',
                ),
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\\"clearfix\\"></div>',
            ),
        );
    }
}