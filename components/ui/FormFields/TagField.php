<?php

class TagField extends FormField {

    public $type = 'TagField';
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
    public static $toolbarName = "Tag Field";
    public static $category = "User Interface";
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
        return ['tag-field.js'];
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
                'label'      => 'Field Name',
                'name'       => 'name',
                'options'    => array(
                    'ng-model'  => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list'   => 'modelFieldList',
                ),
                'menuPos'    => 'pull-right',
                'list'       => array(),
                'searchable' => 'Yes',
                'showOther'  => 'Yes',
                'type'       => 'DropDownList',
            ),
            array(
                'label'   => 'Label',
                'name'    => 'label',
                'options' => array(
                    'ng-model'  => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay'  => '500',
                ),
                'type'    => 'TextField',
            ),
            array(
                'label'      => 'Layout',
                'name'       => 'layout',
                'options'    => array(
                    'ng-model'  => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr'   => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type'       => 'DropDownList',
            ),
            array(
                'column1' => array(
                    array(
                        'label'      => 'Label Width',
                        'name'       => 'labelWidth',
                        'layout'     => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options'    => array(
                            'ng-model'    => 'active.labelWidth',
                            'ng-change'   => 'save()',
                            'ng-delay'    => '500',
                            'ng-disabled' => 'active.layout == \\\'Vertical\\\'',
                        ),
                        'type'       => 'TextField',
                    ),
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array(
                    array(
                        'label'      => 'Field Width',
                        'name'       => 'fieldWidth',
                        'layout'     => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options'    => array(
                            'ng-model'  => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay'  => '500',
                        ),
                        'type'       => 'TextField',
                    ),
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column3' => array(
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column4' => array(
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1'      => '50%',
                'w2'      => '50%',
                'type'    => 'ColumnField',
            ),
            array(
                'type'  => 'Text',
                'value' => '<hr/>',
            ),
            array(
                'label'      => 'Mode',
                'name'       => 'mode',
                'options'    => array(
                    'ng-model'  => 'active.fieldType',
                    'ng-change' => 'save();',
                ),
                'list'       => array(
                    'normal' => 'Normal',
                    'rel'    => 'Relation',
                ),
                'showOther'  => 'Yes',
                'otherLabel' => 'Other...',
                'type'       => 'DropDownList',
            ),
            array(
                'type'  => 'Text',
                'value' => '<div ng-if=\"active.mode == \'rel\'\">
<hr/>',
            ),
            array(
                'name'    => 'TypeRelation',
                'subForm' => 'application.components.ui.FormFields.TextFieldRelation',
                'type'    => 'SubForm',
            ),
            array(
                'type'  => 'Text',
                'value' => '</div>
<div ng-if=\"active.mode == \'normal\'\">
<hr/>',
            ),
            array(
                'label'     => 'PHP Expression',
                'fieldname' => 'acPHP',
                'type'      => 'ExpressionField',
            ),
            array(
                'type'  => 'Text',
                'value' => '</div>
<hr/>',
            ),
            array(
                'label' => 'Options',
                'name'  => 'options',
                'type'  => 'KeyValueGrid',
            ),
            array(
                'label' => 'Label Options',
                'name'  => 'labelOptions',
                'type'  => 'KeyValueGrid',
            ),
            array(
                'label' => 'Field Options',
                'name'  => 'fieldOptions',
                'type'  => 'KeyValueGrid',
            ),
        );
    }

}
