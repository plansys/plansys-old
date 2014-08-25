<?php

class LinkButton extends FormField {

    public $label = '';
    public $url = '#';
    public $urlparams = array();
    public $group = '';
    public $groupType = 'ButtonGroup';
    public $buttonType = 'success';
    public $icon = '';
    public $buttonSize = 'btn-sm';
    public $options = array();
    public $displayInline = true;
    public static $toolbarName = "Link Button";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-unlink";

    public function includeJS() {
        return array('link-button.js');
    }

    public function createUrl($url) {
        if ($url == "#") {
            return "#";
        } else {
            return Yii::app()->controller->createUrl($url);
        }
    }

    public function getFieldProperties() {
        return array (
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
                'label' => 'Group',
                'name' => 'group',
                'options' => array (
                    'ng-model' => 'active.group',
                    'ng-change' => 'save();console.log($event);',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Group Type',
                'name' => 'groupType',
                'options' => array (
                    'ng-show' => 'active.group.trim() != \'\'',
                    'ng-model' => 'active.groupType',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'ButtonGroup\\\', \\\'DropDown\\\')',
                'type' => 'DropDownList',
            ),
            '<Hr/>',
            array (
                'label' => 'Url',
                'name' => 'url',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Url Parameters',
                'fieldname' => 'urlparams',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            '<Hr/>',
            array (
                'label' => 'Icon',
                'name' => 'icon',
                'fieldWidth' => '7',
                'prefix' => 'fa-',
                'options' => array (
                    'ng-model' => 'active.icon',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Button Type',
                'name' => 'buttonType',
                'options' => array (
                    'ng-model' => 'active.buttonType',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'listExpr' => 'array(
     \'primary\' => \'Primary\',
     \'info\' => \'Info\',
     \'success\' => \'Success\',
     \'warning\' => \'Warning\',
     \'danger\' => \'Danger\',
     \'default\' => \'Default\',
     \'not-btn\' => \'Not Button\',
);',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Button Size',
                'name' => 'buttonSize',
                'options' => array (
                    'ng-model' => 'active.buttonSize',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'btn-xs' => 'Very Small',
                    'btn-sm' => 'Small',
                    '' => 'Default',
                    'btn-lg' => 'Large',
                ),
                'listExpr' => 'array(
    \'btn-xs\' => \'Very Small\',
    \'btn-sm\' => \'Small\',
    \'\' => \'Default\',
    \'btn-lg\' => \'Large\',
)',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}
