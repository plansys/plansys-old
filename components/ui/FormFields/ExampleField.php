<?php

class ExampleField extends FormField {

    public $type = 'ExampleField';
    public $name = '';
    public $value = '';
    public $mode = 'normal';
    public $label = '';
    public $layout = 'Vertical';
    public $labelWidth = 4;
    public $fieldWidth = 8;
    public $options = [];
    public $labelOptions = [];
    public $fieldOptions = [];
    public static $toolbarName = "Example Field";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-tags";


    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    public function includeJS() {
        return ['example-field.js'];
    }

    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->renderID;
        $this->fieldOptions['name'] = $this->renderName;
        $this->addClass('form-control', 'fieldOptions');
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        if (!is_string($this->value))
            $this->value = json_encode($this->value);

        return $this->renderInternal('template_render.php');
    }

    public function getFieldProperties() {
        return array(
            array(
                'label'   => 'Name',
                'name'    => 'name',
                'options' => array(
                    'ng-model'  => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay'  => '500',
                ),
                'type'    => 'TextField',
            ),
            array(
                'label' => 'Options',
                'name'  => 'options',
                'type'  => 'KeyValueGrid',
            ),
        );
    }

}
