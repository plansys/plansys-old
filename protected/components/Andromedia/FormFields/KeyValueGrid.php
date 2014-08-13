<?php

class KeyValueGrid extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Active FieldName',
                'name' => 'fieldname',
                'options' => array (
                    'ng-model' => 'active.fieldname',
                    'ng-change' => 'save()',
                    'ng-form-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'list' => array (),
                'layout' => 'Vertical',
                'labelWidth' => '12',
                'fieldWidth' => '12',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            '<hr/>',
            array (
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Grid Initial State',
                'name' => 'show',
                'options' => array (
                    'ng-model' => 'active.show',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'Show' => 'Show',
                    'Hide' => 'Hide',
                ),
                'listExpr' => 'array(
   \'Show\',
   \'Hide\',
)',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Empty Key',
                'name' => 'allowEmptyKey',
                'options' => array (
                    'ng-model' => 'active.allowEmptyKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'listExpr' => 'array(\'No\',\'Yes\');',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Space in Key',
                'name' => 'allowSpaceOnKey',
                'options' => array (
                    'ng-model' => 'active.allowSpaceOnKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'listExpr' => 'array(\'No\',\'Yes\');',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Double Quote',
                'name' => 'allowDoubleQuote',
                'options' => array (
                    'ng-model' => 'active.allowDoubleQuote',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'listExpr' => 'array(\'No\',\'Yes\')',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'active.options',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public $label = '';
    public $fieldname = '';
    public $value = '';
    public $show = 'Hide';
    public $options = array();
    public $allowEmptyKey = 'No';
    public $allowSpaceOnKey = 'No';
    public $allowDoubleQuote = 'No';
    public static $toolbarName = "KeyValue Grid";
    public static $category = "Developer Fields";
    public static $toolbarIcon = "fa fa-key";

    public function includeJS() {
        return array('key-value-grid.js');
    }

    public function render() {
        if ($this->fieldname != '') {
            $this->options['ng-model'] = 'active.' . $this->fieldname;
        }
        return $this->renderInternal('template_render.php');
    }

}
