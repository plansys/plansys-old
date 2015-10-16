<?php

class DevCrudRelBelongsTo extends Form {

    public $formType  = "";

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
        );
    }

}