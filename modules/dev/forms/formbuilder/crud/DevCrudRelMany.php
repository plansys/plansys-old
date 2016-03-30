<?php

class DevCrudRelMany extends Form {

    public $formType = 'Table';
    public $chooseable = 'No';
    public $uniqueEntry = 'No';
    public $editable = 'No';
    public $insertable = 'No';
    public $deleteable = 'No';

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
                'type' => 'Text',
                'value' => '<hr ng-if=\"model.type == \'CManyManyRelation\'\">
',
            ),
            array (
                'label' => 'Chooseable',
                'name' => 'chooseable',
                'options' => array (
                    'ng-if' => 'model.formType == \'Table\' && model.type == \'CManyManyRelation\'',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\'No\', \'Yes\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Unique Entry',
                'name' => 'uniqueEntry',
                'options' => array (
                    'ng-if' => 'model.type == \'CManyManyRelation\' && model.chooseable == \'Yes\'',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\'No\',\'Yes\']',
                'type' => 'DropDownList',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<hr>',
            ),
            array (
                'label' => 'Editable',
                'name' => 'editable',
                'options' => array (
                    'ng-if' => 'model.formType == \'Table\'',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\'No\', \'PopUp\', \'Inline\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Insertable',
                'name' => 'insertable',
                'options' => array (
                    'ng-if' => 'model.formType == \'Table\' && (model.type != \'CManyManyRelation\' || (model.type == \'CManyManyRelation\' && model.chooseable == \'Yes\'))',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\'No\',\'PopUp\', \'Inline\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Deleteable',
                'name' => 'deleteable',
                'options' => array (
                    'ng-if' => 'model.formType == \'Table\' && model.type == \'CHasManyRelation\'',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\'No\', \'Yes\']',
                'type' => 'DropDownList',
            ),
        );
    }
}