<?php

class AceEditor extends FormField {

    public $type = 'AceEditor';
    public $name = '';
    public $label = '';
    public $options = [];
    public $containerOptions = [];
    public static $toolbarName = "Ace Editor";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-code";

    public function includeJS() {
        return ['ace-editor.js'];
    }

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'list' => array (),
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Ace Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'KeyValue Grid',
                'name' => 'containerOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}