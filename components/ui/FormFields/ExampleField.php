<?php

class ExampleField extends FormField {

    public $type = 'ExampleField';
    public $name = '';
    public $value = '';
    public $options = [];
    public static $toolbarName = "Example Field";
    public static $category = "Layout";
    public static $toolbarIcon = "glyphicon glyphicon-gift";

    public function includeJS() {
        return ['example-field.js'];
    }

    public function render() {
        $this->options['style'] = 'border:1px solid #ddd;margin:10px;padding:10px';
        $this->addClass('text-center', 'options');

        $this->value = 'Default Value Example'; //set default value

        return $this->renderInternal('template_render.php');
    }

    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Name',
                'name' => 'name',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}
