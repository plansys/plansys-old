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
                    'ng-change' => 'save(list)',
                    'ng-delay' => '500',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Url',
                'name' => 'url',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'fieldHeight' => '',
                'options' => array (
                    'ng-model' => 'active.url',
                    'ng-change' => 'save(list)',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'auto-grow' => '',
                    'style' => 'word-break:break-all;',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Menu Icon',
                'name' => 'icon',
                'listExpr' => 'Helper::iconList()',
                'renderEmpty' => 'Yes',
                'layout' => 'Vertical',
                'iconTemplate' => '<i class=\\"fa fa-fw fa-lg {icon}\\"></i>',
                'fieldWidth' => '400',
                'options' => array (
                    'ng-model' => 'active.icon',
                    'ng-change' => 'save(list)',
                ),
                'type' => 'IconPicker',
            ),
        );
    }
    
}