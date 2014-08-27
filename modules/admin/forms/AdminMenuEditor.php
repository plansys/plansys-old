<?php

class AdminMenuEditor extends Form {
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
                'totalColumns' => '3',
                'column1' => array (
                    array (
                        'label' => 'Icon',
                        'name' => 'icon',
                        'list' => array (
                            '' => '<i class="fa "></i> - NONE -',
                            'fa-adjust' => '<i class="fa fa-adjust"></i> Adjust',
                            'fa-adn' => '<i class="fa fa-adn"></i> Adn',
                            'fa-align-center' => '<i class="fa fa-align-center"></i> Align center',
                            'fa-align-justify' => '<i class="fa fa-align-justify"></i> Align justify',
                            'z...' => '...',
                        ),
                        'listExpr' => 'Helper::iconList()',
                        'renderEmpty' => 'Yes',
                        'iconTemplate' => '<i class=\\"fa fa-fw fa-lg {icon}\\"></i>',
                        'fieldWidth' => '400',
                        'options' => array (
                            'ng-model' => 'active.icon',
                            'ng-change' => 'save()',
                        ),
                        'type' => 'IconPicker',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'label' => 'State',
                        'name' => 'Drop Down List',
                        'options' => array (
                            'ng-model' => 'active.state',
                            'ng-change' => 'save()',
                            'ng-show' => 'active.items.length>0',
                        ),
                        'list' => array (
                            '' => 'Expanded',
                            'collapsed' => 'Collapsed',
                        ),
                        'listExpr' => 'array(
  \'\'=>\'Expanded\', 
  \'collapsed\'=>\'Collapsed\'
)',
                        'type' => 'DropDownList',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Display Logic',
                'fieldname' => 'visible',
                'type' => 'ExpressionField',
                'options' => array(
                    'ps-valid' => 'active.visible = result;'
                )
            ),
        );
    }
    
}
