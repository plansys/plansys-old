<?php

class DevCrudRelMany extends Form {

    public $formType = 'Table';
    public $chooseable = 'No';
    public $editable = 'No';
    public $insertable = 'No';

    public function getForm() {
        return array (
            'title' => 'Crud Rel Many',
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
                'listExpr' => '[\'Table\', \'SubForm\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Chooseable',
                'name' => 'chooseable',
                'options' => array (
                    'ng-if' => 'model.type == \'CManyManyRelation\'',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Editable',
                'name' => 'editable',
                'defaultType' => 'first',
                'listExpr' => '[\'No\', \'PopUp\', \'Inline\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Insertable',
                'name' => 'insertable',
                'defaultType' => 'first',
                'listExpr' => '[\'No\',\'PopUp\', \'Inline\']',
                'type' => 'DropDownList',
            ),
        );
    }
}