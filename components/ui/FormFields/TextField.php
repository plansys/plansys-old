<?php

/**
 * Class TextField
 * @author rizky
 */
class TextField extends FormField {

    /**
     * @return array me-return array property TextField.
     */
    public function getFieldProperties() {
        return  [
             [
                'label' => 'Field Name',
                'name' => 'name',
                'options' =>  [
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ],
                'list' =>  [],
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Label',
                'name' => 'label',
                'options' =>  [
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Field Type',
                'name' => 'fieldType',
                'options' =>  [
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save();',
                ],
                'list' =>  [
                    'text' => 'Text Field',
                    'password' => 'Password Field',
                ],
                'showOther' => 'Yes',
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Layout',
                'name' => 'layout',
                'options' =>  [
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ],
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ],
             [
                'column1' =>  [
                     [
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' =>  [
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \\\'Vertical\\\'',
                        ],
                        'type' => 'TextField',
                    ],
                     [
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ],
                ],
                'column2' =>  [
                     [
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' =>  [
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ],
                        'type' => 'TextField',
                    ],
                     [
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ],
                ],
                'column3' =>  [
                     [
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ],
                ],
                'column4' =>  [
                     [
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ],
                ],
                'type' => 'ColumnField',
            ],
             [
                'type' => 'Text',
                'value' => '<hr/>',
            ],
             [
                'column1' =>  [
                     [
                        'name' => 'prefix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Prefix',
                        'options' =>  [
                            'ng-model' => 'active.prefix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ],
                        'type' => 'TextField',
                    ],
                     [
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ],
                ],
                'column2' =>  [
                     [
                        'name' => 'postfix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Postfix',
                        'options' =>  [
                            'ng-model' => 'active.postfix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ],
                        'type' => 'TextField',
                    ],
                     [
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ],
                ],
                'type' => 'ColumnField',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ],
        ];
    }

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name = '';

    /** @var string $fieldType */
    public $fieldType = 'text';

    /** @var string $value */
    public $value = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var string $prefix */
    public $prefix = '';

    /** @var string $postfix */
    public $postfix = '';

    /** @var array $options */
    public $options = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Text Field";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-text-height";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['text-field.js'];
    }

    /**
     * getLayoutClass
     * Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout
     * @return string me-return string Class layout yang digunakan
     */
    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    /**
     * @return string me-return string Class error jika terdapat error pada satu atau banyak attribute.
     */
    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    /**
     * getlabelClass
     * Fungsi ini akan mengecek $layout untuk menentukan layout yang digunakan
     * dan me-load option label dari property $labelOptions
     * @return string me-return string Class label
     */
    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    /**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
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

}