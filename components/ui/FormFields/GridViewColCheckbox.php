<?php

class  GridViewColCheckbox extends Form {

    public $checkedValue = '';
    public $uncheckedValue = '';

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
            'inlineJS' => '',
        );
    }

    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div style=\\"height:10px;\\"></div>',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div style=\"font-size:12px;color:#999;position:absolute;margin:-2px 0px;padding:0px 5px;background:#fff;\">Checked</div>
<hr/>',
            ),
            array (
                'label' => 'Value',
                'name' => 'checkedValue',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'item.checkedValue',
                    'ng-delay' => '500',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'TextField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div style=\\"height:10px;\\"></div>',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div style=\"font-size:12px;color:#999;position:absolute;margin:-2px 0px;padding:0px 5px;background:#fff;\">Unchecked</div>
<hr/>',
            ),
            array (
                'label' => 'Value',
                'name' => 'uncheckedValue',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'item.uncheckedValue',
                    'ng-change' => 'updateListView()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
        );
    }

}