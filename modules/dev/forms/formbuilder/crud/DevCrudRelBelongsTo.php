<?php

class DevCrudRelBelongsTo extends Form {

    public $formType  = "";
    public $deleteable = "Yes";
    public $insertable = "Yes";

    public function getForm() {
        return array (
            'title' => 'Crud Rel Belongs To',
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
                'label' => 'Form Type',
                'name' => 'formType',
                'defaultType' => 'first',
                'listExpr' => '[
  \'PopUp\' => \'PopUp\',
  \'SubForm\' => \'SubForm\'
]',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr>
<div ng-if=\"model.formType == \'PopUp\'\">',
            ),
            array (
                'label' => 'Delete-able',
                'name' => 'deleteable',
                'defaultType' => 'first',
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Insert-able',
                'name' => 'insertable',
                'defaultType' => 'first',
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}