<?php

class DevCrudRelBelongsTo extends Form {

    public $formType  = "";
    public $deleteable = "Yes";
    public $insertable = "Yes";
    public $editable  = "No";

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
                'label' => 'Deleteable',
                'name' => 'deleteable',
                'defaultType' => 'first',
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Insertable',
                'name' => 'insertable',
                'defaultType' => 'first',
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"model.formType == \'SubForm\'\\">',
            ),
            array (
                'label' => 'Editable',
                'name' => 'editable',
                'defaultType' => 'first',
                'listExpr' => '[\'No\',\'Yes\']',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}