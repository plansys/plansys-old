<?php

class TextField extends FormField {

    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-form-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'list' => array(),
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Field Type',
                'name' => 'fieldType',
                'options' => array(
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save();',
                ),
                'list' => array(
                    'text' => 'Text Field',
                    'password' => 'Password Field',
                ),
                'showOther' => 'Yes',
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Label',
                'name' => 'label',
                'options' => array(
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array(
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'list' => array(
                    'Horizontal' => 'Horizontal',
                    'Vertical' => 'Vertical',
                ),
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array(
                'column1' => array(
                    array(
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array(
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array(
                    array(
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' => array(
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column3' => array(
                    '<column-placeholder></column-placeholder>',
                ),
                'column4' => array(
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            '<hr/>',
            array(
                'column1' => array(
                    array(
                        'name' => 'prefix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Prefix',
                        'options' => array(
                            'ng-model' => 'active.prefix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array(
                    array(
                        'name' => 'postfix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Postfix',
                        'options' => array(
                            'ng-model' => 'active.postfix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            array(
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array(
                'label' => 'Label Options',
                'fieldname' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array(
                'label' => 'Field Options',
                'fieldname' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public $label = '';
    public $name = '';
    public $fieldType = 'text';
    public $value = '';
    public $layout = 'Horizontal';
    public $labelWidth = 4;
    public $fieldWidth = 8;
    public $prefix = '';
    public $postfix = '';
    public $options = array();
    public $labelOptions = array();
    public $fieldOptions = array();
    public static $toolbarName = "Text Field";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-text-height";

    public function includeJS() {
        return array('text-field.js');
    }

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

    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->name;
        $this->fieldOptions['name'] = $this->name;
        $this->addClass('form-control', 'fieldOptions');

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        return $this->renderInternal('template_render.php');
    }

}
