<?php

class DevMenuEditor extends Form {
    public function getForm() {
        return array (
            'title' => 'MenuEditor',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'name' => 'col1',
                    ),
                ),
            ),
        );
    }
    public $label;
    public $icon;
    public $url;
    public $visible;
    public $state = "Expanded";
    
    public function getFields() {
        return array (
            array (
                'label' => 'Label',
                'name' => 'label',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Url',
                'name' => 'url',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'active.url',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'State',
                        'name' => 'Drop Down List',
                        'options' => array (
                            'ng-model' => 'active.state',
                            'ng-change' => 'save()',
                            'ng-show' => 'active.items.length>0',
                        ),
                        'listExpr' => 'array(
  \'\'=>\'Expanded\', 
  \'collapsed\'=>\'Collapsed\'
)',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Icon',
                        'name' => 'icon',
                        'listExpr' => 'Helper::iconList()',
                        'renderEmpty' => 'Yes',
                        'iconTemplate' => '<i class=\\"fa fa-fw fa-lg {icon}\\"></i>',
                        'fieldWidth' => '400',
                        'options' => array (
                            'ng-model' => 'active.icon',
                            'ng-change' => 'save()',
                            'ng-show' => 'active.items.length==0',
                        ),
                        'type' => 'IconPicker',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Display Logic',
                'fieldname' => 'visible',
                'options' => array (
                    'ps-valid' => 'active.visible = result;',
                ),
                'type' => 'ExpressionField',
            ),
        );
    }
    
}